<?php

namespace App\Jobs\QA;

use App\Models\QA\QaCall;
use App\Services\QA\ClaudeService;
use App\Services\QA\DeepgramService;
use App\Services\QA\GeminiService;
use App\Services\QA\QAResultService;
use App\Services\QA\QAScoringPrompt;
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
    public int $timeout = 600;
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

        $localPath = null;

        try {
            // ── Step 1: Download Recording ──────────────────────────────
            $qaCall->update(['processing_status' => 'downloading']);

            $localPath = storage_path('app/qa_recordings/' . $qaCall->zoom_call_id . '.mp3');
            $zoomOAuth = app(ZoomOAuthService::class);
            $zoomOAuth->downloadRecording($qaCall->recording_url, $localPath);

            $qaCall->update(['local_recording_path' => $localPath]);

            Log::info('[QA:Job] Recording downloaded', [
                'qa_call_id' => $qaCall->id,
                'path' => $localPath,
            ]);

            // ── Step 2: Filter short calls ──────────────────────────────
            // Double-check duration from file if needed. If duration < 3 min, skip.
            if ($qaCall->duration_seconds < 180) {
                $qaCall->update([
                    'processing_status' => 'skipped',
                    'failure_reason' => 'Call under 3 minutes (' . $qaCall->duration_seconds . 's) — likely a hang-up',
                ]);
                $this->cleanupFile($localPath);
                Log::info('[QA:Job] Skipped short call', ['duration' => $qaCall->duration_seconds]);
                return;
            }

            // ── Step 3: Transcribe ──────────────────────────────────────
            // Primary: Local Whisper (free, no API cost)
            // Fallback: Deepgram API (if Whisper fails or is disabled)
            $qaCall->update(['processing_status' => 'transcribing']);

            $transcriptionEngine = config('services.whisper.enabled', true) ? 'whisper' : 'deepgram';
            $transcript = null;

            if ($transcriptionEngine === 'whisper') {
                try {
                    $whisper = app(WhisperService::class);
                    $transcript = $whisper->transcribe($localPath);
                    Log::info('[QA:Job] Whisper transcription succeeded', ['qa_call_id' => $qaCall->id]);
                } catch (\Throwable $e) {
                    Log::warning('[QA:Job] Whisper failed, falling back to Deepgram', [
                        'qa_call_id' => $qaCall->id,
                        'whisper_error' => $e->getMessage(),
                    ]);
                    $transcriptionEngine = 'deepgram';
                }
            }

            if (!$transcript) {
                $deepgram = app(DeepgramService::class);
                $transcript = $deepgram->transcribe($localPath);
                Log::info('[QA:Job] Deepgram transcription succeeded', ['qa_call_id' => $qaCall->id]);
            }

            $qaCall->update([
                'transcript_plain' => $transcript['plain'],
                'transcript_diarized' => $transcript['diarized'],
            ]);

            Log::info('[QA:Job] Transcription complete', [
                'qa_call_id' => $qaCall->id,
                'engine' => $transcriptionEngine,
                'plain_length' => strlen($transcript['plain']),
                'diarized_length' => strlen($transcript['diarized']),
            ]);

            // ── Step 4: Score with AI ───────────────────────────────────
            $qaCall->update(['processing_status' => 'scoring']);

            $prompt = QAScoringPrompt::build($transcript['diarized'], $qaCall->duration_seconds);
            $scoredBy = 'gemini';
            $aiResult = null;

            // Try Gemini first (primary)
            try {
                $gemini = app(GeminiService::class);
                $aiResult = $gemini->scoreCall($prompt);
                $scoredBy = 'gemini';
            } catch (\Throwable $e) {
                Log::warning('[QA:Job] Gemini failed, falling back to Claude', [
                    'qa_call_id' => $qaCall->id,
                    'gemini_error' => $e->getMessage(),
                ]);

                // Fallback to Claude
                $claude = app(ClaudeService::class);
                $aiResult = $claude->scoreCall($prompt);
                $scoredBy = 'claude';
            }

            $qaCall->update(['scored_by' => $scoredBy]);

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
