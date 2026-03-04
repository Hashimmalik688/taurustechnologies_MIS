<?php

namespace App\Services\QA;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class WhisperService
{
    private string $pythonBin;
    private string $scriptPath;
    private string $model;
    private ?string $hfToken;
    private int $cpuThreads;
    private int $batchSize;

    public function __construct()
    {
        $this->pythonBin = config('services.whisper.python_bin', '/opt/whisperx-env/bin/python');
        $this->scriptPath = base_path('scripts/whisper_transcribe.py');
        $this->model = config('services.whisper.model', 'large-v2');
        $this->hfToken = config('services.whisper.hf_token');
        $this->cpuThreads = (int) config('services.whisper.cpu_threads', 8);
        $this->batchSize = (int) config('services.whisper.batch_size', 8);
    }

    /**
     * Transcribe an audio file using WhisperX (faster-whisper + wav2vec2 alignment + pyannote diarization).
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

        Log::info('[QA:WhisperX] Starting transcription + diarization', [
            'file' => basename($filePath),
            'model' => $this->model,
            'diarization' => $this->hfToken ? 'pyannote' : 'heuristic',
        ]);

        $startTime = microtime(true);

        // Build environment variables for the Python script
        $env = [
            'WHISPER_MODEL' => $this->model,
            'WHISPER_CPU_THREADS' => (string) $this->cpuThreads,
            'WHISPER_BATCH_SIZE' => (string) $this->batchSize,
            'HF_HOME' => storage_path('app/.cache'),
            'TORCH_HOME' => storage_path('app/.cache/torch'),
        ];

        if ($this->hfToken) {
            $env['HF_TOKEN'] = $this->hfToken;
        }

        // WhisperX pipeline: transcribe -> align -> diarize (needs CPU power, no throttling)
        // Timeout: 1800s (30 min) — large-v2 + diarization on CPU can take time for long calls
        $result = Process::timeout(1800)
            ->env($env)
            ->run([
                $this->pythonBin,
                $this->scriptPath,
                $filePath,
                '--model', $this->model,
                '--language', 'en',
            ]);

        $elapsed = round(microtime(true) - $startTime, 1);

        if (!$result->successful()) {
            Log::error('[QA:WhisperX] Transcription failed', [
                'exit_code' => $result->exitCode(),
                'stderr' => substr($result->errorOutput(), -2000),
                'elapsed' => $elapsed . 's',
            ]);
            throw new \RuntimeException('WhisperX transcription failed: ' . $result->errorOutput());
        }

        $output = trim($result->output());
        $data = json_decode($output, true);

        if (!$data || !isset($data['plain'])) {
            Log::error('[QA:WhisperX] Invalid JSON output', ['raw' => substr($output, 0, 500)]);
            throw new \RuntimeException('WhisperX returned invalid output');
        }

        Log::info('[QA:WhisperX] Transcription + diarization complete', [
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
