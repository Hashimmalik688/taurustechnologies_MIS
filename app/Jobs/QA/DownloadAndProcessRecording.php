<?php

namespace App\Jobs\QA;

use App\Models\QA\QaCall;
use App\Services\QA\ClaudeService;
use App\Services\QA\GeminiService;
use App\Services\QA\QAResultService;
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

    public int $tries = 6;
    public int $timeout = 600; // 10 minutes — Zoom transcripts only (no WhisperX)
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

        try {
            // ── Step 1: Fetch Zoom's built-in transcript (required) ──
            // Try two routes in order:
            //   A. zoom_transcript_url stored on the record (from phone.recording_transcript_completed webhook)
            //   B. Fetch from Zoom Phone API using zoom_call_log_id
            // If neither yields a transcript yet, release the job for retry (transcript may still be generating).
            if (!$hasPlainTranscript) {
                $qaCall->update(['processing_status' => 'transcribing']);
                $zoomOAuth = app(ZoomOAuthService::class);
                $zoomTranscript = null;

                // Route A: direct URL already captured from transcript_completed webhook
                if ($qaCall->zoom_transcript_url) {
                    Log::info('[QA:Job] Using stored zoom_transcript_url', [
                        'qa_call_id' => $qaCall->id,
                        'url'        => substr($qaCall->zoom_transcript_url, 0, 80) . '...',
                    ]);
                    $zoomTranscript = $zoomOAuth->downloadTranscriptFromUrl(
                        $qaCall->zoom_transcript_url,
                        $qaCall->agent_name ?? ''
                    );
                }

                // Route B: pull from Zoom Phone recordings API via call log ID
                if (!$zoomTranscript && $qaCall->zoom_call_log_id) {
                    $zoomTranscript = $zoomOAuth->fetchZoomTranscript(
                        $qaCall->zoom_call_log_id,
                        $qaCall->agent_name ?? ''
                    );
                }

                if ($zoomTranscript) {
                    $qaCall->update([
                        'transcript_plain'    => $zoomTranscript,
                        'transcript_diarized' => $zoomTranscript,
                        'transcript_source'   => 'zoom',
                    ]);
                    $hasPlainTranscript = true;

                    Log::info('[QA:Job] Fetched Zoom transcript', [
                        'qa_call_id' => $qaCall->id,
                        'chars'      => strlen($zoomTranscript),
                    ]);
                }
            }

            // ── Step 2: Verify transcript available ────────────────────────────
            // Zoom transcripts can take 1-15 minutes to generate after the recording completes.
            // Release the job back to delayed queue (5-min gap) rather than hard-failing.
            if (!$hasPlainTranscript) {
                if ($this->attempts() < $this->tries) {
                    Log::info('[QA:Job] Transcript not ready yet — releasing for retry in 5 min', [
                        'qa_call_id' => $qaCall->id,
                        'attempt'    => $this->attempts(),
                    ]);
                    $qaCall->update(['processing_status' => 'pending']);
                    $this->release(300); // retry in 5 minutes
                    return;
                }

                $error = 'No Zoom transcript available after ' . $this->tries . ' attempts';
                $qaCall->update([
                    'processing_status' => 'failed',
                    'failure_reason'    => $error,
                ]);
                Log::error('[QA:Job] Call failed — transcript never arrived', [
                    'qa_call_id' => $qaCall->id,
                ]);
                return;
            }

            // ── Step 3: AI Scoring ────────────────────────────────────────────
            // Zoom transcripts have AGENT:/CUSTOMER: labels from VTT parser.
            // AI analyzes pre-labeled content without need for diarization.
            $qaCall->update(['processing_status' => 'scoring']);

            $transcriptForAi = $qaCall->transcript_plain;
            $aiResult = null;
            $scoredBy = 'gemini';

            try {
                $aiResult = app(ClaudeService::class)->analyzePreLabeledCall($transcriptForAi, $qaCall->duration_seconds);
                $scoredBy = 'claude';
            } catch (\Throwable $e) {
                Log::warning('[QA:Job] Claude failed, falling back to Gemini', [
                    'qa_call_id' => $qaCall->id,
                    'error'      => $e->getMessage(),
                ]);
                $aiResult = app(GeminiService::class)->analyzePreLabeledCall($transcriptForAi, $qaCall->duration_seconds);
                $scoredBy = 'gemini';
            }

            $qaCall->update(['scored_by' => $scoredBy]);

            Log::info('[QA:Job] AI analysis complete', [
                'qa_call_id'       => $qaCall->id,
                'scored_by'        => $scoredBy,
                'transcript_source'=> $qaCall->transcript_source,
                'disposition'      => $aiResult['disposition'] ?? 'unknown',
                'total_score'      => $aiResult['total_score'] ?? 0,
                'agent_turns'      => substr_count($qaCall->fresh()->transcript_diarized ?? '', 'AGENT:'),
                'customer_turns'   => substr_count($qaCall->fresh()->transcript_diarized ?? '', 'CUSTOMER:'),
            ]);

            // ── Step 4: Save Results ────────────────────────────────────
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
