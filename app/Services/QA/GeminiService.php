<?php

namespace App\Services\QA;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model = 'gemini-2.5-flash';
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * Use Gemini to label speaker turns in a plain transcript.
     *
     * Handles three speaker types:
     *   AGENT:     — the insurance sales rep (speaks first, drives the call)
     *   CUSTOMER:  — the elderly US citizen being called
     *   [BANK IVR] — automated bank phone system audio (for bank verification 3-way calls)
     *
     * @param string $plainTranscript  Raw WhisperX output (no speaker labels)
     * @param int    $durationSeconds  Call duration (for context)
     * @return string Labeled transcript
     */
    public function diarizeTranscript(string $plainTranscript, int $durationSeconds): string
    {
        $durationMin = round($durationSeconds / 60, 1);

        $prompt = <<<PROMPT
You are labeling speakers in a recorded outbound phone call transcript.

SITUATION:
- This is an outbound Final Expense life insurance sales call from a US call center
- The AGENT is a sales closer (often speaks with a South Asian accent)
- The CUSTOMER is an elderly US citizen (often confused, gives short replies like "Yes", "No", "Okay", "Yeah", "Uh-huh", "Right", "Mm-hmm", "I don't know", "I can't")
- Call duration: {$durationMin} minutes
- Sometimes the agent performs a BANK VERIFICATION by conferencing in the customer's bank IVR phone system — that automated bank audio must be labeled [BANK IVR]

THREE SPEAKER LABELS (use exactly these):
  AGENT:     for the insurance sales rep
  CUSTOMER:  for the elderly customer
  [BANK IVR] for automated bank phone system messages (e.g. "Thank you for calling Bank of America", hold messages, "Please enter your account number", "I'm not able to find that number", "For quality purposes your call may be recorded", etc.)

RULES:
1. Do NOT change, add, or remove any words from the transcript
2. Start a new line whenever the speaker changes
3. IDENTIFY the AGENT by: they introduce themselves by name ("My name is [NAME]", "This is [NAME] calling from..."), mention a company, ask "Am I speaking with [NAME]?" or address the customer as Mr./Mrs. — this is the most reliable signal. The customer may say "Hello?" or "Yes?" when answering BEFORE the agent speaks their first full sentence.
4. Short responses ("Yes", "No", "Okay", "Yeah", "Uh-huh", "Mm-hmm", "Right", "I don't know", "I can't", "Hello?", "Who is this?") → almost always CUSTOMER
5. Long selling sentences, health questions, policy details, banking instructions, reading back account numbers → almost always AGENT
6. ANY automated bank system audio (hold music narration, IVR prompts, "Welcome to Bank of America", "Please hold", "For quality purposes", etc.) → always [BANK IVR]
7. When the agent is reading bank account numbers or routing numbers BACK to the customer to confirm, that is still AGENT
8. If the agent says something like "here we go" or "just stay with me" while conferencing the bank, those words are AGENT; the bank audio that follows is [BANK IVR]

Return ONLY the labeled transcript — no JSON, no explanations, no commentary.
Every line must start with exactly "AGENT: ", "CUSTOMER: ", or "[BANK IVR] ".

TRANSCRIPT:
---
{$plainTranscript}
---
PROMPT;

        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        Log::info('[QA:Gemini] Diarizing transcript', [
            'plain_length' => strlen($plainTranscript),
            'duration_min' => $durationMin,
        ]);

        $response = Http::timeout(120)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature'      => 0.1,
                    'maxOutputTokens'  => 16384,
                    // Plain text output — no JSON for diarization
                ],
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Gemini diarization error: ' . $response->status() . ' - ' . $response->body());
        }

        $text = trim($response->json('candidates.0.content.parts.0.text') ?? '');

        if (empty($text)) {
            throw new \RuntimeException('Gemini returned empty diarization response');
        }

        Log::info('[QA:Gemini] Diarization complete', [
            'labeled_length' => strlen($text),
            'agent_turns'    => substr_count($text, "\nAGENT:") + (str_starts_with($text, 'AGENT:') ? 1 : 0),
            'customer_turns' => substr_count($text, "\nCUSTOMER:") + (str_starts_with($text, 'CUSTOMER:') ? 1 : 0),
            'bank_ivr_turns' => substr_count($text, '[BANK IVR]'),
        ]);

        return $text;
    }

    /**
     * Score a call transcript using Gemini.
     *
     * @param string $prompt The complete QA scoring prompt with transcript
     * @return array Parsed JSON scoring result
     * @throws \RuntimeException
     */
    public function scoreCall(string $prompt): array
    {
        Log::info('[QA:Gemini] FALLBACK — Sending scoring request', [
            'model' => $this->model,
            'prompt_length' => strlen($prompt),
        ]);

        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::timeout(120)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'responseMimeType' => 'application/json',
                    'maxOutputTokens' => 8192,
                ],
            ]);

        if (!$response->successful()) {
            Log::error('[QA:Gemini] API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Gemini API error: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();

        // Extract text from Gemini response structure
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$text) {
            Log::error('[QA:Gemini] No text in response', ['response' => $data]);
            throw new \RuntimeException('Gemini returned empty response');
        }

        // Parse JSON — Gemini with responseMimeType should return clean JSON
        $parsed = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Try to extract JSON from potential markdown wrapping
            $parsed = $this->extractJsonFromText($text);
        }

        if (!$parsed) {
            Log::error('[QA:Gemini] Failed to parse JSON response', ['text' => $text]);
            throw new \RuntimeException('Gemini returned invalid JSON: ' . substr($text, 0, 200));
        }

        Log::info('[QA:Gemini] FALLBACK scoring complete', [
            'disposition' => $parsed['disposition'] ?? 'unknown',
            'total_score' => $parsed['total_score'] ?? 0,
        ]);

        return $parsed;
    }

    /**
     * ONE-SHOT: Diarize speakers AND score the call in a single Gemini API call.
     *
     * Takes a PLAIN (unlabeled) transcript and returns the full QA result array
     * plus a "diarized_transcript" field with speaker-labeled transcript.
     *
     * This replaces the old two-step (diarizeTranscript → scoreCall) pipeline:
     *   - ~40% fewer input tokens (transcript sent once, not twice)
     *   - One API round-trip saved (~30-60 seconds)
     *   - AI understands speaker context while scoring (better accuracy)
     *
     * @param string $plainTranscript  Raw WhisperX output (no speaker labels)
     * @param int    $durationSeconds  Call duration
     * @return array Full QA result including 'diarized_transcript' key
     */
    public function analyzeCall(string $plainTranscript, int $durationSeconds): array
    {
        $prompt = QAScoringPrompt::buildCombined($plainTranscript, $durationSeconds);

        Log::info('[QA:Gemini] PRIMARY analyzeCall — diarize+score in one shot', [
            'model'          => $this->model,
            'prompt_length'  => strlen($prompt),
            'duration_min'   => round($durationSeconds / 60, 1),
        ]);

        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::timeout(300)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature'     => 0.1,
                    'maxOutputTokens' => 32768, // Needs to fit labeled transcript (~15k) + JSON (~2k)
                    'responseMimeType' => 'application/json',
                ],
            ]);

        if (!$response->successful()) {
            Log::error('[QA:Gemini] analyzeCall API error', [
                'status' => $response->status(),
                'body'   => substr($response->body(), 0, 500),
            ]);
            throw new \RuntimeException('Gemini analyzeCall error: ' . $response->status() . ' - ' . $response->body());
        }

        $text = $response->json('candidates.0.content.parts.0.text') ?? null;

        if (!$text) {
            throw new \RuntimeException('Gemini analyzeCall returned empty response');
        }

        $parsed = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $parsed = $this->extractJsonFromText($text);
        }

        if (!$parsed) {
            Log::error('[QA:Gemini] analyzeCall invalid JSON', ['text' => substr($text, 0, 500)]);
            throw new \RuntimeException('Gemini analyzeCall returned invalid JSON');
        }

        Log::info('[QA:Gemini] analyzeCall complete', [
            'disposition'    => $parsed['disposition'] ?? 'unknown',
            'total_score'    => $parsed['total_score'] ?? 0,
            'agent_turns'    => substr_count($parsed['diarized_transcript'] ?? '', 'AGENT:'),
            'customer_turns' => substr_count($parsed['diarized_transcript'] ?? '', 'CUSTOMER:'),
            'bank_ivr_turns' => substr_count($parsed['diarized_transcript'] ?? '', '[BANK IVR]'),
        ]);

        return $parsed;
    }

    /**
     * Score a pre-labeled transcript from Zoom (scoring only — no diarization).
     *
     * Used when Zoom's built-in transcription already produced AGENT:/CUSTOMER: labels.
     * Skips the speaker-identification step and goes straight to QA evaluation,
     * saving ~40% of the token budget vs analyzeCall().
     *
     * @param string $labeledTranscript  Transcript already labeled with AGENT:/CUSTOMER: prefixes
     * @param int    $durationSeconds    Call duration
     * @return array Standard QA result array (no 'diarized_transcript' key)
     */
    public function analyzePreLabeledCall(string $labeledTranscript, int $durationSeconds): array
    {
        $prompt = QAScoringPrompt::build($labeledTranscript, $durationSeconds);

        Log::info('[QA:Gemini] PRIMARY analyzePreLabeledCall — score only (Zoom transcript)', [
            'model'         => $this->model,
            'prompt_length' => strlen($prompt),
            'duration_min'  => round($durationSeconds / 60, 1),
        ]);

        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::timeout(300)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature'     => 0.1,
                    'maxOutputTokens' => 8192,
                    'responseMimeType' => 'application/json',
                ],
            ]);

        if (!$response->successful()) {
            Log::error('[QA:Gemini] analyzePreLabeledCall API error', [
                'status' => $response->status(),
                'body'   => substr($response->body(), 0, 500),
            ]);
            throw new \RuntimeException('Gemini analyzePreLabeledCall error: ' . $response->status() . ' - ' . $response->body());
        }

        $text = $response->json('candidates.0.content.parts.0.text') ?? null;

        if (!$text) {
            throw new \RuntimeException('Gemini analyzePreLabeledCall returned empty response');
        }

        $parsed = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $parsed = $this->extractJsonFromText($text);
        }

        if (!$parsed) {
            Log::error('[QA:Gemini] analyzePreLabeledCall invalid JSON', ['text' => substr($text, 0, 500)]);
            throw new \RuntimeException('Gemini analyzePreLabeledCall returned invalid JSON');
        }

        Log::info('[QA:Gemini] analyzePreLabeledCall complete', [
            'disposition' => $parsed['disposition'] ?? 'unknown',
            'total_score' => $parsed['total_score'] ?? 0,
        ]);

        return $parsed;
    }

    /**
     * Try to extract JSON from text that may have markdown code blocks or preamble.
     */
    private function extractJsonFromText(string $text): ?array
    {
        // Try stripping markdown code blocks
        if (preg_match('/```(?:json)?\s*\n?(.*?)\n?```/s', $text, $matches)) {
            $decoded = json_decode($matches[1], true);
            if ($decoded) return $decoded;
        }

        // Try finding JSON object in text
        if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
            $decoded = json_decode($matches[0], true);
            if ($decoded) return $decoded;
        }

        return null;
    }
}
