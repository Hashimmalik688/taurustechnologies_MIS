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
                'max_tokens' => 4096,
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
