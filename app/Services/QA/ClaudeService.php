<?php

namespace App\Services\QA;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    private string $apiKey;
    private string $model = 'claude-haiku-4-5-20251001';
    private string $baseUrl = 'https://api.anthropic.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key');
    }

    /**
     * Use Claude to label speaker turns in a plain transcript.
     * Returns the transcript with AGENT: / CUSTOMER: prefixes on each turn.
     *
     * This replaces pyannote diarization — Claude understands call context far
     * better than a pause-based heuristic or an unconfigured ML model.
     *
     * @param string $plainTranscript  Raw WhisperX output (no speaker labels)
     * @param int    $durationSeconds  Call duration (for context)
     * @return string Labeled transcript with AGENT:/CUSTOMER: on each turn
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
3. The AGENT always speaks first
4. Short responses ("Yes", "No", "Okay", "Yeah", "Uh-huh", "Mm-hmm", "Right", "I don't know", "I can't", "I don't have it") → almost always CUSTOMER
5. Long selling sentences, health questions, policy details, banking instructions → almost always AGENT
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

        Log::info('[QA:Claude] Diarizing transcript', [
            'plain_length' => strlen($plainTranscript),
            'duration_min' => $durationMin,
        ]);

        $response = Http::timeout(120)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])
            ->post($this->baseUrl . '/messages', [
                'model' => $this->model,
                'max_tokens' => 8192,
                'temperature' => 0.1,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Claude diarization error: ' . $response->status() . ' - ' . $response->body());
        }

        $text = trim($response->json('content.0.text') ?? '');

        if (empty($text)) {
            throw new \RuntimeException('Claude returned empty diarization response');
        }

        Log::info('[QA:Claude] Diarization complete', [
            'labeled_length' => strlen($text),
            'agent_turns' => substr_count($text, "\nAGENT:") + (str_starts_with($text, 'AGENT:') ? 1 : 0),
            'customer_turns' => substr_count($text, "\nCUSTOMER:") + (str_starts_with($text, 'CUSTOMER:') ? 1 : 0),
        ]);

        return $text;
    }

    /**
     * Score a call transcript using Claude (fallback).
     *
     * @param string $prompt The complete QA scoring prompt with transcript
     * @return array Parsed JSON scoring result
     * @throws \RuntimeException
     */
    public function scoreCall(string $prompt): array
    {
        Log::info('[QA:Claude] PRIMARY — Sending scoring request', [
            'model' => $this->model,
            'prompt_length' => strlen($prompt),
        ]);

        $response = Http::timeout(120)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])
            ->post($this->baseUrl . '/messages', [
                'model' => $this->model,
                'max_tokens' => 8192,
                'temperature' => 0.1,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

        if (!$response->successful()) {
            Log::error('[QA:Claude] API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Claude API error: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();

        // Extract text from Claude response
        $text = $data['content'][0]['text'] ?? null;

        if (!$text) {
            Log::error('[QA:Claude] No text in response', ['response' => $data]);
            throw new \RuntimeException('Claude returned empty response');
        }

        // Parse JSON from Claude's response
        $parsed = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $parsed = $this->extractJsonFromText($text);
        }

        if (!$parsed) {
            Log::error('[QA:Claude] Failed to parse JSON response', ['text' => $text]);
            throw new \RuntimeException('Claude returned invalid JSON: ' . substr($text, 0, 200));
        }

        Log::info('[QA:Claude] PRIMARY scoring complete', [
            'disposition' => $parsed['disposition'] ?? 'unknown',
            'total_score' => $parsed['total_score'] ?? 0,
        ]);

        return $parsed;
    }

    /**
     * ONE-SHOT FALLBACK: Diarize speakers AND score the call in a single Claude API call.
     *
     * Used when Gemini is unavailable. Same logic as GeminiService::analyzeCall().
     * Note: Claude Haiku may truncate very long transcripts (>65 min calls).
     * Gemini is the preferred primary for analyzeCall.
     *
     * @param string $plainTranscript  Raw WhisperX output (no speaker labels)
     * @param int    $durationSeconds  Call duration
     * @return array Full QA result including 'diarized_transcript' key
     */
    public function analyzeCall(string $plainTranscript, int $durationSeconds): array
    {
        $prompt = QAScoringPrompt::buildCombined($plainTranscript, $durationSeconds);

        Log::info('[QA:Claude] FALLBACK analyzeCall — diarize+score in one shot', [
            'model'         => $this->model,
            'prompt_length' => strlen($prompt),
            'duration_min'  => round($durationSeconds / 60, 1),
        ]);

        $response = Http::timeout(300)
            ->withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type'      => 'application/json',
            ])
            ->post($this->baseUrl . '/messages', [
                'model'      => $this->model,
                'max_tokens' => 16384, // Larger limit needed for labeled transcript + JSON
                'temperature' => 0.1,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

        if (!$response->successful()) {
            Log::error('[QA:Claude] analyzeCall API error', [
                'status' => $response->status(),
                'body'   => substr($response->body(), 0, 500),
            ]);
            throw new \RuntimeException('Claude analyzeCall error: ' . $response->status() . ' - ' . $response->body());
        }

        $text = $response->json('content.0.text') ?? null;

        if (!$text) {
            throw new \RuntimeException('Claude analyzeCall returned empty response');
        }

        $parsed = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $parsed = $this->extractJsonFromText($text);
        }

        if (!$parsed) {
            Log::error('[QA:Claude] analyzeCall invalid JSON', ['text' => substr($text, 0, 500)]);
            throw new \RuntimeException('Claude analyzeCall returned invalid JSON');
        }

        Log::info('[QA:Claude] analyzeCall complete', [
            'disposition'    => $parsed['disposition'] ?? 'unknown',
            'total_score'    => $parsed['total_score'] ?? 0,
            'agent_turns'    => substr_count($parsed['diarized_transcript'] ?? '', 'AGENT:'),
            'customer_turns' => substr_count($parsed['diarized_transcript'] ?? '', 'CUSTOMER:'),
        ]);

        return $parsed;
    }

    /**
     * Try to extract JSON from text that may have markdown or preamble.
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
