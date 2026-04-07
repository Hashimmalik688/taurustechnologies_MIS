<?php

namespace App\Services\QA;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    private string $apiKey;
    private string $model = 'claude-sonnet-4-6';
    private string $baseUrl = 'https://api.anthropic.com/v1';

    /**
     * System prompt injected on every QA scoring request.
     * Sets the model's role before it sees any user content, improving
     * consistency and preventing preamble/markdown in the output.
     */
    private const SYSTEM_PROMPT = 'You are a certified QA analyst for a life insurance resale call center. ' .
        'You evaluate outbound sales calls using a precise scoring rubric. ' .
        'Your evaluations must be consistent, evidence-based, and anchored to the specific behavioral descriptions in the rubric. ' .
        'You always return a single valid JSON object exactly as specified — no markdown, no preamble, no trailing text. ' .
        'Never invent transcript content. If something is unclear or inaudible, default to the most favorable reasonable interpretation for the closer.';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key');
    }

    /**
     * Score a call transcript using Claude (primary).
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
                'system' => self::SYSTEM_PROMPT,
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
     * Score a pre-labeled transcript from Zoom (scoring only — no diarization).
     *
     * PRIMARY scorer. Zoom's built-in transcription already provides AGENT:/CUSTOMER: labels.
     * Falls back to GeminiService::analyzePreLabeledCall() if this throws.
     *
     * @param string $labeledTranscript  Transcript with AGENT:/CUSTOMER: prefixes
     * @param int    $durationSeconds    Call duration
     * @return array Standard QA result array
     */
    public function analyzePreLabeledCall(string $labeledTranscript, int $durationSeconds): array
    {
        $prompt = QAScoringPrompt::build($labeledTranscript, $durationSeconds);

        Log::info('[QA:Claude] PRIMARY analyzePreLabeledCall — score only (Zoom transcript)', [
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
                'model'       => $this->model,
                'max_tokens'  => 8192,
                'temperature' => 0.1,
                'system'      => self::SYSTEM_PROMPT,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

        if (!$response->successful()) {
            Log::error('[QA:Claude] analyzePreLabeledCall API error', [
                'status' => $response->status(),
                'body'   => substr($response->body(), 0, 500),
            ]);
            throw new \RuntimeException('Claude analyzePreLabeledCall error: ' . $response->status() . ' - ' . $response->body());
        }

        $text = $response->json('content.0.text') ?? null;

        if (!$text) {
            throw new \RuntimeException('Claude analyzePreLabeledCall returned empty response');
        }

        $parsed = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $parsed = $this->extractJsonFromText($text);
        }

        if (!$parsed) {
            Log::error('[QA:Claude] analyzePreLabeledCall invalid JSON', ['text' => substr($text, 0, 500)]);
            throw new \RuntimeException('Claude analyzePreLabeledCall returned invalid JSON');
        }

        Log::info('[QA:Claude] analyzePreLabeledCall complete', [
            'disposition' => $parsed['disposition'] ?? 'unknown',
            'total_score' => $parsed['total_score'] ?? 0,
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
