<?php

namespace App\Services\QA;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class WhisperService
{
    private string $pythonBin;
    private string $scriptPath;
    private string $model;

    public function __construct()
    {
        $this->pythonBin = config('services.whisper.python_bin', '/usr/bin/python3');
        $this->scriptPath = base_path('scripts/whisper_transcribe.py');
        $this->model = config('services.whisper.model', 'distil-large-v3');
    }

    /**
     * Transcribe an audio file using local Whisper (faster-whisper).
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

        if (!file_exists($this->scriptPath)) {
            throw new \RuntimeException("Whisper script not found: {$this->scriptPath}");
        }

        Log::info('[QA:Whisper] Starting local transcription', [
            'file' => basename($filePath),
            'model' => $this->model,
        ]);

        $startTime = microtime(true);

        // Run with nice (low priority) so it doesn't starve live calls/web server.
        // nice -n 15 = low CPU priority; ionice -c3 = idle-only disk I/O
        $result = Process::timeout(900)
            ->run([
                'nice', '-n', '15',
                'ionice', '-c3',
                $this->pythonBin,
                $this->scriptPath,
                $filePath,
                '--model', $this->model,
                '--language', 'en',
            ]);

        $elapsed = round(microtime(true) - $startTime, 1);

        if (!$result->successful()) {
            Log::error('[QA:Whisper] Transcription failed', [
                'exit_code' => $result->exitCode(),
                'stderr' => $result->errorOutput(),
                'elapsed' => $elapsed . 's',
            ]);
            throw new \RuntimeException('Whisper transcription failed: ' . $result->errorOutput());
        }

        $output = trim($result->output());
        $data = json_decode($output, true);

        if (!$data || !isset($data['plain'])) {
            Log::error('[QA:Whisper] Invalid JSON output', ['raw' => substr($output, 0, 500)]);
            throw new \RuntimeException('Whisper returned invalid output');
        }

        Log::info('[QA:Whisper] Transcription complete', [
            'file' => basename($filePath),
            'model' => $this->model,
            'elapsed' => $elapsed . 's',
            'plain_length' => strlen($data['plain']),
            'diarized_length' => strlen($data['diarized']),
        ]);

        return [
            'plain' => $data['plain'],
            'diarized' => $data['diarized'],
        ];
    }
}
