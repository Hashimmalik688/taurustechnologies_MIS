<?php

namespace App\Services\QA;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepgramService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.deepgram.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.deepgram.api_key');
    }

    /**
     * Transcribe an audio file using Deepgram Nova-2.
     *
     * @param string $filePath Absolute path to the audio file
     * @return array{plain: string, diarized: string}
     * @throws \RuntimeException
     */
    public function transcribe(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Audio file not found: {$filePath}");
        }

        Log::info('[QA:Deepgram] Starting transcription', ['file' => basename($filePath)]);

        $response = Http::timeout(300)
            ->withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
                'Content-Type' => $this->getMimeType($filePath),
            ])
            ->withBody(file_get_contents($filePath), $this->getMimeType($filePath))
            ->post($this->baseUrl . '/listen', [
                'model' => 'nova-2',
                'diarize' => 'true',
                'punctuate' => 'true',
                'utterances' => 'true',
                'smart_format' => 'true',
                'language' => 'en-US',
            ]);

        if (!$response->successful()) {
            Log::error('[QA:Deepgram] API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Deepgram API error: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();

        return [
            'plain' => $this->extractPlainText($data),
            'diarized' => $this->extractDiarizedText($data),
        ];
    }

    /**
     * Extract plain concatenated transcript from Deepgram response.
     */
    private function extractPlainText(array $data): string
    {
        $channels = $data['results']['channels'] ?? [];

        if (empty($channels)) {
            return '';
        }

        $alternatives = $channels[0]['alternatives'] ?? [];

        if (empty($alternatives)) {
            return '';
        }

        return $alternatives[0]['transcript'] ?? '';
    }

    /**
     * Extract diarized transcript with AGENT:/CUSTOMER: labels.
     * Speaker 0 = AGENT, Speaker 1 = CUSTOMER
     */
    private function extractDiarizedText(array $data): string
    {
        $utterances = $data['results']['utterances'] ?? [];

        if (empty($utterances)) {
            // Fall back to word-level diarization
            return $this->buildDiarizedFromWords($data);
        }

        $lines = [];
        foreach ($utterances as $utterance) {
            $speaker = ($utterance['speaker'] ?? 0) === 0 ? 'AGENT' : 'CUSTOMER';
            $text = trim($utterance['transcript'] ?? '');
            if ($text) {
                $lines[] = "{$speaker}: {$text}";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Build diarized text from word-level speaker data when utterances aren't available.
     */
    private function buildDiarizedFromWords(array $data): string
    {
        $channels = $data['results']['channels'] ?? [];
        if (empty($channels)) return '';

        $words = $channels[0]['alternatives'][0]['words'] ?? [];
        if (empty($words)) return '';

        $lines = [];
        $currentSpeaker = null;
        $currentText = '';

        foreach ($words as $word) {
            $speaker = ($word['speaker'] ?? 0) === 0 ? 'AGENT' : 'CUSTOMER';
            $punctuatedWord = $word['punctuated_word'] ?? $word['word'] ?? '';

            if ($speaker !== $currentSpeaker) {
                if ($currentSpeaker !== null && trim($currentText)) {
                    $lines[] = "{$currentSpeaker}: " . trim($currentText);
                }
                $currentSpeaker = $speaker;
                $currentText = $punctuatedWord;
            } else {
                $currentText .= ' ' . $punctuatedWord;
            }
        }

        // Append last segment
        if ($currentSpeaker !== null && trim($currentText)) {
            $lines[] = "{$currentSpeaker}: " . trim($currentText);
        }

        return implode("\n", $lines);
    }

    private function getMimeType(string $filePath): string
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return match ($ext) {
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'flac' => 'audio/flac',
            'm4a' => 'audio/mp4',
            'webm' => 'audio/webm',
            default => 'audio/mpeg',
        };
    }
}
