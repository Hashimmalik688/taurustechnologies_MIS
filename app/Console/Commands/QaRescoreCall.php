<?php

namespace App\Console\Commands;

use App\Models\QA\QaCall;
use App\Models\QA\QaResult;
use App\Services\QA\ClaudeService;
use App\Services\QA\GeminiService;
use App\Services\QA\QAResultService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class QaRescoreCall extends Command
{
    protected $signature = 'qa:rescore {call_id : QaCall ID to re-score}
                            {--dry-run : Show what would happen without writing to DB}';

    protected $description = 'Re-score a QA call with the current prompt (useful after prompt corrections)';

    public function handle(): int
    {
        $callId = (int) $this->argument('call_id');
        $dryRun = $this->option('dry-run');

        $qaCall = QaCall::find($callId);
        if (!$qaCall) {
            $this->error("QaCall #{$callId} not found.");
            return self::FAILURE;
        }

        // Require a stored transcript
        $transcript = $qaCall->transcript_diarized;
        if (!$transcript || trim($transcript) === '') {
            $this->error("QaCall #{$callId} has no stored diarized transcript. Cannot re-score.");
            return self::FAILURE;
        }

        $existingResult = QaResult::where('qa_call_id', $callId)->first();

        $this->info("QaCall #{$callId} — Agent: {$qaCall->agent_name}");
        $this->info("  Duration : {$qaCall->duration_seconds}s");
        $this->info("  Transcript length : " . strlen($transcript) . " chars");
        if ($existingResult) {
            $this->info("  Current disposition : {$existingResult->disposition} | score : {$existingResult->total_score} | compliance : " . ($existingResult->compliance_pass ? 'PASS' : 'FAIL'));
            $this->info("  Current void_risk_reason : " . substr($existingResult->void_risk_reason ?? 'none', 0, 120));
        } else {
            $this->warn("  No existing result found — will create fresh.");
        }

        if ($dryRun) {
            $this->warn("[DRY-RUN] No changes will be written.");
            return self::SUCCESS;
        }

        $this->line('  Sending to AI for re-scoring...');

        try {
            $aiResult = app(ClaudeService::class)->analyzePreLabeledCall($transcript, $qaCall->duration_seconds ?? 0);
            $this->info('  [Claude] Re-score complete.');
        } catch (\Throwable $e) {
            $this->warn("  [Claude] Failed: {$e->getMessage()} — falling back to Gemini...");
            try {
                $aiResult = app(GeminiService::class)->analyzePreLabeledCall($transcript, $qaCall->duration_seconds ?? 0);
                $this->info('  [Gemini] Re-score complete.');
            } catch (\Throwable $e2) {
                $this->error("  All AI providers failed: {$e2->getMessage()}");
                return self::FAILURE;
            }
        }

        $this->line('  Saving result...');
        $resultService = new QAResultService();
        $qaResult      = $resultService->saveResult($qaCall, $aiResult);

        $this->info("  ✓ Re-scored successfully.");
        $this->table(
            ['Field', 'Before', 'After'],
            [
                ['Disposition',    $existingResult?->disposition    ?? '—', $qaResult->disposition],
                ['Score',          $existingResult?->total_score    ?? '—', $qaResult->total_score],
                ['Compliance',     $existingResult ? ($existingResult->compliance_pass ? 'PASS' : 'FAIL') : '—', $qaResult->compliance_pass ? 'PASS' : 'FAIL'],
                ['Void Reason',    substr($existingResult?->void_risk_reason ?? 'none', 0, 60), substr($qaResult->void_risk_reason ?? 'none', 0, 60)],
            ]
        );

        Log::info('[QA:Rescore] Call re-scored via artisan', [
            'qa_call_id'      => $callId,
            'old_disposition' => $existingResult?->disposition,
            'new_disposition' => $qaResult->disposition,
            'old_score'       => $existingResult?->total_score,
            'new_score'       => $qaResult->total_score,
            'rescored_by'     => 'artisan:qa:rescore',
        ]);

        return self::SUCCESS;
    }
}
