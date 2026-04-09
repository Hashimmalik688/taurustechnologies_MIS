<?php

namespace App\Services\QA;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AssemblyAIService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.assemblyai.com/v2';

    public function __construct()
    {
        $this->apiKey = config('services.assemblyai.api_key');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 1 — Upload audio file to AssemblyAI and get an upload_url
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Upload a local file path or an UploadedFile to AssemblyAI's storage.
     *
     * @param  string|UploadedFile $file  Local path OR a Laravel UploadedFile
     * @return string  The upload_url returned by AssemblyAI
     * @throws \RuntimeException
     */
    public function uploadAudio(string|UploadedFile $file): string
    {
        if ($file instanceof UploadedFile) {
            $contents = file_get_contents($file->getRealPath());
            $filename = $file->getClientOriginalName();
        } else {
            $contents = file_get_contents($file);
            $filename = basename($file);
        }

        Log::info('[AssemblyAI] Uploading audio', ['filename' => $filename, 'bytes' => strlen($contents)]);

        $response = Http::timeout(120)
            ->withHeaders([
                'authorization' => $this->apiKey,
                'Content-Type'  => 'application/octet-stream',
            ])
            ->withBody($contents, 'application/octet-stream')
            ->post($this->baseUrl . '/upload');

        if (!$response->successful()) {
            throw new \RuntimeException('AssemblyAI upload failed: ' . $response->status() . ' ' . $response->body());
        }

        $uploadUrl = $response->json('upload_url');

        if (!$uploadUrl) {
            throw new \RuntimeException('AssemblyAI upload returned no upload_url: ' . $response->body());
        }

        Log::info('[AssemblyAI] Upload successful', ['upload_url' => substr($uploadUrl, 0, 60) . '…']);

        return $uploadUrl;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 2 — Submit a transcription job with speaker diarization
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Start a transcription job. Returns the transcript ID.
     *
     * @param  string $audioUrl  Either an AssemblyAI upload_url or any public URL
     * @param  array  $options   Extra AssemblyAI transcription options to merge in
     * @return string  The transcript ID (poll this until status = 'completed')
     * @throws \RuntimeException
     */
    public function submitTranscription(string $audioUrl, array $options = []): string
    {
        $payload = array_merge([
            'audio_url'          => $audioUrl,
            'speaker_labels'     => true,      // Diarization — identifies speakers
            'speakers_expected'  => 2,         // Typically Agent + Customer
            'punctuate'          => true,
            'format_text'        => true,
            'filter_profanity'   => false,
        ], $options);

        Log::info('[AssemblyAI] Submitting transcription job', ['audio_url' => substr($audioUrl, 0, 60) . '…']);

        $response = Http::timeout(30)
            ->withHeaders([
                'authorization' => $this->apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->post($this->baseUrl . '/transcript', $payload);

        if (!$response->successful()) {
            throw new \RuntimeException('AssemblyAI transcript submit failed: ' . $response->status() . ' ' . $response->body());
        }

        $transcriptId = $response->json('id');

        if (!$transcriptId) {
            throw new \RuntimeException('AssemblyAI returned no transcript ID: ' . $response->body());
        }

        Log::info('[AssemblyAI] Transcription job queued', ['transcript_id' => $transcriptId]);

        return $transcriptId;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 3 — Poll transcript status
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Fetch the current status and results of a transcript job.
     *
     * Possible status values: queued | processing | completed | error
     *
     * @param  string $transcriptId
     * @return array  Full AssemblyAI transcript object
     * @throws \RuntimeException
     */
    public function getTranscript(string $transcriptId): array
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'authorization' => $this->apiKey,
            ])
            ->get($this->baseUrl . '/transcript/' . $transcriptId);

        if (!$response->successful()) {
            throw new \RuntimeException('AssemblyAI poll failed: ' . $response->status() . ' ' . $response->body());
        }

        return $response->json();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Synchronous convenience: upload + submit + poll until done
    // Use only for short calls (<5 min) in synchronous contexts.
    // For longer calls, use the async flow (submit → poll via /status endpoint).
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Upload audio, submit transcription, and poll until completed.
     * Maximum wait: ~10 minutes (120 × 5-second polls).
     *
     * @param  string|UploadedFile $file
     * @return array  ['transcript_id', 'text', 'diarized', 'utterances', 'duration_seconds']
     * @throws \RuntimeException on error or timeout
     */
    public function transcribeSync(string|UploadedFile $file): array
    {
        $uploadUrl    = $this->uploadAudio($file);
        $transcriptId = $this->submitTranscription($uploadUrl);

        return $this->pollUntilComplete($transcriptId);
    }

    /**
     * Poll AssemblyAI until the transcript is completed or errored.
     *
     * @param  string $transcriptId
     * @param  int    $maxAttempts  Poll attempts before giving up (default 120 = ~10 min)
     * @param  int    $intervalSec  Seconds to sleep between polls
     * @return array  ['transcript_id', 'text', 'diarized', 'utterances', 'duration_seconds']
     * @throws \RuntimeException
     */
    public function pollUntilComplete(string $transcriptId, int $maxAttempts = 120, int $intervalSec = 5): array
    {
        Log::info('[AssemblyAI] Polling transcript', ['transcript_id' => $transcriptId]);

        for ($i = 0; $i < $maxAttempts; $i++) {
            $data = $this->getTranscript($transcriptId);
            $status = $data['status'] ?? 'unknown';

            if ($status === 'completed') {
                return $this->parseTranscriptResult($data);
            }

            if ($status === 'error') {
                throw new \RuntimeException('AssemblyAI transcription error: ' . ($data['error'] ?? 'unknown error'));
            }

            Log::debug('[AssemblyAI] Still processing…', [
                'transcript_id' => $transcriptId,
                'attempt'       => $i + 1,
                'status'        => $status,
            ]);

            sleep($intervalSec);
        }

        throw new \RuntimeException("AssemblyAI transcription timed out after {$maxAttempts} attempts ({$transcriptId})");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Parse AssemblyAI response into the QA diarized format used by Claude
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Parse a completed AssemblyAI transcript into our internal format.
     *
     * Uses the `utterances` array (speaker_labels output) to produce
     * AGENT:/CUSTOMER: prefixed lines compatible with ClaudeService.
     *
     * Speaker A = Agent (first speaker in most call-center recordings)
     * Speaker B = Customer
     * Additional speakers get SPEAKER_C, etc.
     *
     * @param  array $data  Raw AssemblyAI transcript object
     * @return array ['transcript_id', 'text', 'diarized', 'utterances', 'duration_seconds']
     */
    public function parseTranscriptResult(array $data): array
    {
        $utterances = $data['utterances'] ?? [];
        $lines      = [];
        $diarized   = [];

        // Determine speaker mapping.
        // If there are exactly 2 speakers (A & B), A = AGENT, B = CUSTOMER.
        // This matches how outbound call-center calls are typically recorded
        // (agent's audio track comes first).
        // If AssemblyAI detects 3+ speakers (acoustic variation of the same person),
        // we collapse them: first speaker = AGENT, all others = CUSTOMER.
        $speakers = collect($utterances)->pluck('speaker')->unique()->sort()->values()->toArray();
        $speakerMap = [];
        foreach ($speakers as $idx => $speaker) {
            $speakerMap[$speaker] = ($idx === 0) ? 'AGENT' : 'CUSTOMER';
        }

        foreach ($utterances as $utterance) {
            $speaker  = $utterance['speaker'] ?? 'A';
            $label    = $speakerMap[$speaker] ?? 'AGENT';
            $text     = trim($utterance['text'] ?? '');
            $start    = $utterance['start'] ?? 0; // milliseconds
            $end      = $utterance['end']   ?? 0;

            if (!$text) continue;

            $lines[]    = ['speaker' => $label, 'text' => $text, 'start_ms' => $start, 'end_ms' => $end];
            $diarized[] = "{$label}: {$text}";
        }

        // Duration: AssemblyAI returns audio_duration in seconds
        $durationSeconds = (int) round($data['audio_duration'] ?? 0);

        Log::info('[AssemblyAI] Transcript parsed', [
            'transcript_id'   => $data['id'],
            'duration_sec'    => $durationSeconds,
            'utterances'      => count($lines),
            'speakers'        => $speakerMap,
        ]);

        return [
            'transcript_id'    => $data['id'],
            'text'             => $data['text'] ?? '',           // plain continuous text
            'diarized'         => implode("\n", $diarized),      // AGENT:/CUSTOMER: format
            'utterances'       => $lines,
            'duration_seconds' => $durationSeconds,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Speaker label override — swap AGENT/CUSTOMER if recording is reversed
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Swap AGENT and CUSTOMER labels in a diarized transcript.
     * Used when the recording has the customer's track as Speaker A.
     */
    public static function swapSpeakers(string $diarized): string
    {
        // Use a placeholder to avoid double-replacement
        $result = str_replace('AGENT:', '__PLACEHOLDER_AGENT__:', $diarized);
        $result = str_replace('CUSTOMER:', 'AGENT:', $result);
        $result = str_replace('__PLACEHOLDER_AGENT__:', 'CUSTOMER:', $result);
        return $result;
    }
}
