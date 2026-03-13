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
     * Score a pre-labeled transcript from Zoom (scoring only — no diarization).
     *
     * Used when Zoom's built-in transcription already produced AGENT:/CUSTOMER: labels.
     * Skips the speaker-identification step and goes straight to QA evaluation.
     *
     * @param string $labeledTranscript  Transcript already labeled with AGENT:/CUSTOMER: prefixes
     * @param int    $durationSeconds    Call duration
     * @return array Standard QA result array (no 'diarized_transcript' key)
     */
    public function analyzePreLabeledCall(string $labeledTranscript, int $durationSeconds): array
    {
        $prompt = QAScoringPrompt::build($labeledTranscript, $durationSeconds);

        Log::info('[QA:Gemini] FALLBACK analyzePreLabeledCall — score only (Zoom transcript)', [
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
