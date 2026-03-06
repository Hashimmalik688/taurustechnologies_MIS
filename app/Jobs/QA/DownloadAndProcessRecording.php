<?php

namespace App\Jobs\QA;

use App\Models\QA\QaCall;
use App\Services\QA\ClaudeService;
use App\Services\QA\GeminiService;
use App\Services\QA\QAResultService;
use App\Services\QA\WhisperService;
use App\Services\QA\ZoomOAuthService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadAndProcessRecording implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 7200; // 2 hours — large-v2 + diarization on CPU for long calls
    public int $backoff = 60;

    private int $qaCallId;

    public function __construct(int $qaCallId)
    {
        $this->qaCallId = $qaCallId;
        $this->onQueue('qa-processing');
    }

    public function handle(): void
    {
        $qaCall = QaCall::find($this->qaCallId);

        if (!$qaCall) {
            Log::warning('[QA:Job] QaCall not found', ['id' => $this->qaCallId]);
            return;
        }

        // Skip if already completed
        if ($qaCall->processing_status === 'completed') {
            Log::info('[QA:Job] Already completed, skipping', ['id' => $qaCall->id]);
            return;
        }

        $localPath = storage_path('app/qa_recordings/' . $qaCall->zoom_call_id . '.mp3');

        // Idempotent resume: skip stages already completed on a previous attempt.
        $hasPlainTranscript = !empty($qaCall->transcript_plain);
        $needsDownload      = !$hasPlainTranscript && !file_exists($localPath);

        try {
            // ── Step 1: Download Recording ──────────────────────────────
            if ($needsDownload) {
                $qaCall->update(['processing_status' => 'downloading']);

                $zoomOAuth = app(ZoomOAuthService::class);
                $zoomOAuth->downloadRecording(
                    $qaCall->recording_url,
                    $localPath,
                    $qaCall->zoom_call_log_id,
                    $qaCall->zoom_user_id
                );

                $qaCall->update(['local_recording_path' => $localPath]);

                Log::info('[QA:Job] Recording downloaded', [
                    'qa_call_id' => $qaCall->id,
                    'path' => $localPath,
                ]);
            } else {
                Log::info('[QA:Job] Skipping download (file exists or transcript already stored)', [
                    'qa_call_id' => $qaCall->id,
                ]);
            }

            // ── Step 2: Filter short calls ──────────────────────────────
            // Skip calls under 8 minutes — not meaningful sales conversations
            if ($qaCall->duration_seconds < 480) {
                $qaCall->update([
                    'processing_status' => 'skipped',
                    'failure_reason' => 'Call under 8 minutes (' . $qaCall->duration_seconds . 's) — skipped',
                ]);
                $this->cleanupFile($localPath);
                Log::info('[QA:Job] Skipped short call', ['duration' => $qaCall->duration_seconds]);
                return;
            }

            // ── Step 3: Transcribe (WhisperX — local, free) ─────────────
            if ($hasPlainTranscript) {
                Log::info('[QA:Job] Skipping transcription (transcript already stored)', [
                    'qa_call_id' => $qaCall->id,
                ]);
            } elseif ($qaCall->processing_status === 'transcribing' && $qaCall->retry_count > 0) {
                // A prior attempt already started WhisperX — avoid launching a duplicate process.
                Log::warning('[QA:Job] WhisperX already in progress from previous attempt — skipping duplicate launch', [
                    'qa_call_id' => $qaCall->id,
                    'retry_count' => $qaCall->retry_count,
                ]);
                throw new \RuntimeException('WhisperX already running from prior attempt — will retry');
            } else {
                $qaCall->update(['processing_status' => 'transcribing']);

                $whisper = app(WhisperService::class);
                $rawTranscript = $whisper->transcribe($localPath);
                Log::info('[QA:Job] WhisperX transcription succeeded', ['qa_call_id' => $qaCall->id]);

                $qaCall->update([
                    'transcript_plain'    => $rawTranscript['plain'],
                    'transcript_diarized' => '', // will be filled by Gemini analyzeCall below
                ]);

                Log::info('[QA:Job] Transcription complete', [
                    'qa_call_id'   => $qaCall->id,
                    'plain_length' => strlen($rawTranscript['plain']),
                ]);
            }

            // ── Step 4: One-shot AI Analysis (diarize + score together) ────────
            // Gemini reads the plain transcript, identifies speakers, and scores
            // the call in a SINGLE API call.
            //
            // Benefits vs old two-step approach:
            //   - ~40% fewer tokens (transcript sent once, not twice)
            //   - One full API round-trip saved (~30-60 seconds)
            //   - Better speaker attribution (AI understands context while scoring)
            //
            // Fallback: if Gemini fails, Claude does the same single-shot analysis.
            $qaCall->update(['processing_status' => 'scoring']);

            $plainTranscript = $qaCall->transcript_plain;
            $aiResult = null;
            $scoredBy = 'gemini';

            try {
                $gemini = app(GeminiService::class);
                $aiResult = $gemini->analyzeCall($plainTranscript, $qaCall->duration_seconds);
                $scoredBy = 'gemini';
            } catch (\Throwable $e) {
                Log::warning('[QA:Job] Gemini analyzeCall failed, falling back to Claude', [
                    'qa_call_id' => $qaCall->id,
                    'error'      => $e->getMessage(),
                ]);

                $claude = app(ClaudeService::class);
                $aiResult = $claude->analyzeCall($plainTranscript, $qaCall->duration_seconds);
                $scoredBy = 'claude';
            }

            // Store the labeled transcript from the AI response (for display in UI)
            $diarizedTranscript = $aiResult['diarized_transcript'] ?? '';
            if (!empty($diarizedTranscript)) {
                $qaCall->update([
                    'transcript_diarized' => $diarizedTranscript,
                    'scored_by'           => $scoredBy,
                ]);
            } else {
                $qaCall->update(['scored_by' => $scoredBy]);
                Log::warning('[QA:Job] AI returned empty diarized_transcript', ['qa_call_id' => $qaCall->id]);
            }

            Log::info('[QA:Job] AI analysis complete', [
                'qa_call_id'     => $qaCall->id,
                'scored_by'      => $scoredBy,
                'disposition'    => $aiResult['disposition'] ?? 'unknown',
                'total_score'    => $aiResult['total_score'] ?? 0,
                'agent_turns'    => substr_count($diarizedTranscript, 'AGENT:'),
                'customer_turns' => substr_count($diarizedTranscript, 'CUSTOMER:'),
                'bank_ivr_turns' => substr_count($diarizedTranscript, '[BANK IVR]'),
            ]);

            // ── Step 5: Save Results ────────────────────────────────────
            $resultService = app(QAResultService::class);
            $resultService->saveResult($qaCall, $aiResult);

            Log::info('[QA:Job] ✅ Processing complete', [
                'qa_call_id' => $qaCall->id,
                'disposition' => $aiResult['disposition'] ?? 'unknown',
                'total_score' => $aiResult['total_score'] ?? 0,
                'scored_by' => $scoredBy,
            ]);

        } catch (\Throwable $e) {
            Log::error('[QA:Job] Processing failed', [
                'qa_call_id' => $qaCall->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $qaCall->update([
                'processing_status' => 'failed',
                'failure_reason' => substr($e->getMessage(), 0, 255),
                'retry_count' => $qaCall->retry_count + 1,
            ]);

            throw $e; // Re-throw so Laravel retries
        } finally {
            // ── ALWAYS clean up the audio file ──────────────────────────
            $this->cleanupFile($localPath);
        }
    }

    /**
     * Guaranteed file cleanup.
     */
    private function cleanupFile(?string $path): void
    {
        if ($path && file_exists($path)) {
            @unlink($path);
            Log::info('[QA:Job] Audio file cleaned up', ['path' => $path]);
        }
    }

    /**
     * Handle a job that has permanently failed (after all retries exhausted).
     */
    public function failed(\Throwable $exception): void
    {
        $qaCall = QaCall::find($this->qaCallId);

        if ($qaCall) {
            $qaCall->update([
                'processing_status' => 'failed',
                'failure_reason' => 'All retries exhausted: ' . substr($exception->getMessage(), 0, 200),
            ]);
        }

        Log::error('[QA:Job] Permanently failed after all retries', [
            'qa_call_id' => $this->qaCallId,
            'error' => $exception->getMessage(),
        ]);

        // Ensure cleanup even on permanent failure
        $localPath = storage_path('app/qa_recordings/' . ($qaCall?->zoom_call_id ?? 'unknown') . '.mp3');
        if (file_exists($localPath)) {
            @unlink($localPath);
        }
    }
}
