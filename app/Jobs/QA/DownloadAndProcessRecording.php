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
            // ── Step 1: Try Zoom's built-in transcript (free, instant, no GPU needed) ──
            // If Zoom transcription is enabled and a transcript_download_url was captured
            // via the webhook, fetch and use it — skip download + WhisperX entirely.
            if (!$hasPlainTranscript && $qaCall->zoom_call_log_id) {
                $qaCall->update(['processing_status' => 'transcribing']);

                $zoomOAuth = app(ZoomOAuthService::class);
                $zoomTranscript = $zoomOAuth->fetchZoomTranscript(
                    $qaCall->zoom_call_log_id,
                    $qaCall->agent_name ?? ''
                );

                if ($zoomTranscript) {
                    // Store the Zoom-labeled transcript as both plain and diarized.
                    // The VTT parser already produced AGENT:/CUSTOMER: labels,
                    // so AI only needs to score — no re-labeling needed.
                    $qaCall->update([
                        'transcript_plain'    => $zoomTranscript,
                        'transcript_diarized' => $zoomTranscript,
                        'transcript_source'   => 'zoom',
                    ]);
                    $hasPlainTranscript = true;
                    $needsDownload = false;

                    Log::info('[QA:Job] Using Zoom built-in transcript (WhisperX skipped)', [
                        'qa_call_id' => $qaCall->id,
                        'chars'      => strlen($zoomTranscript),
                    ]);
                } else {
                    Log::info('[QA:Job] No Zoom transcript available — will use WhisperX', [
                        'qa_call_id' => $qaCall->id,
                    ]);
                    $needsDownload = !file_exists($localPath);
                }
            }

            // ── Step 2: Download Recording (WhisperX fallback path only) ───────────
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

            // ── Step 3: Filter short calls ──────────────────────────────
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

            // ── Step 4: Transcribe via WhisperX (fallback when Zoom transcript unavailable) ─
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
                    'transcript_source'   => 'whisper',
                ]);

                Log::info('[QA:Job] Transcription complete', [
                    'qa_call_id'   => $qaCall->id,
                    'plain_length' => strlen($rawTranscript['plain']),
                ]);
            }

            // ── Step 5: AI Scoring ───────────────────────────────────────────────
            // TWO paths depending on transcript source:
            //
            // ▶ Zoom transcript (transcript_source = 'zoom'):
            //   VTT already has AGENT:/CUSTOMER: labels — AI only SCORES, no diarization.
            //   Uses QAScoringPrompt::build() via analyzePreLabeledCall().
            //
            // ▶ WhisperX transcript (transcript_source = 'whisper' or null):
            //   Raw unlabeled text — AI diarizes + scores in one shot.
            //   Uses QAScoringPrompt::buildCombined() via analyzeCall().
            $qaCall->update(['processing_status' => 'scoring']);

            $transcriptForAi   = $qaCall->transcript_plain;
            $isZoomTranscript  = $qaCall->transcript_source === 'zoom';
            $aiResult = null;
            $scoredBy = 'gemini';

            try {
                $gemini = app(GeminiService::class);
                if ($isZoomTranscript) {
                    $aiResult = $gemini->analyzePreLabeledCall($transcriptForAi, $qaCall->duration_seconds);
                } else {
                    $aiResult = $gemini->analyzeCall($transcriptForAi, $qaCall->duration_seconds);
                }
                $scoredBy = 'gemini';
            } catch (\Throwable $e) {
                Log::warning('[QA:Job] Gemini failed, falling back to Claude', [
                    'qa_call_id'      => $qaCall->id,
                    'transcript_source' => $qaCall->transcript_source,
                    'error'           => $e->getMessage(),
                ]);

                $claude = app(ClaudeService::class);
                if ($isZoomTranscript) {
                    $aiResult = $claude->analyzePreLabeledCall($transcriptForAi, $qaCall->duration_seconds);
                } else {
                    $aiResult = $claude->analyzeCall($transcriptForAi, $qaCall->duration_seconds);
                }
                $scoredBy = 'claude';
            }

            // For WhisperX calls: store the diarized transcript produced by the AI.
            // For Zoom calls: transcript_diarized was already stored from VTT — just update scored_by.
            if (!$isZoomTranscript) {
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
            } else {
                $qaCall->update(['scored_by' => $scoredBy]);
            }

            Log::info('[QA:Job] AI analysis complete', [
                'qa_call_id'       => $qaCall->id,
                'scored_by'        => $scoredBy,
                'transcript_source'=> $qaCall->transcript_source,
                'disposition'      => $aiResult['disposition'] ?? 'unknown',
                'total_score'      => $aiResult['total_score'] ?? 0,
                'agent_turns'      => substr_count($qaCall->fresh()->transcript_diarized ?? '', 'AGENT:'),
                'customer_turns'   => substr_count($qaCall->fresh()->transcript_diarized ?? '', 'CUSTOMER:'),
            ]);

            // ── Step 6: Save Results ────────────────────────────────────
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
