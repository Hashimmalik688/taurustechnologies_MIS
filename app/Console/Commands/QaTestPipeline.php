<?php

namespace App\Console\Commands;

use App\Models\QA\QaCall;
use App\Models\User;
use App\Services\QA\ClaudeService;
use App\Services\QA\DeepgramService;
use App\Services\QA\GeminiService;
use App\Services\QA\QAResultService;
use App\Services\QA\QAScoringPrompt;
use App\Services\QA\WhisperService;
use Illuminate\Console\Command;

class QaTestPipeline extends Command
{
    protected $signature = 'qa:test
        {audio? : Path to an audio file (mp3/wav/m4a). Optional — uses a built-in sample if omitted}
        {--agent= : User ID of the agent (default: first Employee)}
        {--engine=auto : Transcription engine: whisper, deepgram, or auto}
        {--scorer=claude : AI scorer: claude or gemini}
        {--skip-score : Skip AI scoring (test transcription only)}
        {--dry-run : Show what would happen without saving to DB}';

    protected $description = 'Test the QA pipeline end-to-end: transcribe → score → save. Use to verify Whisper, Gemini/Claude, and the full flow.';

    public function handle(): int
    {
        $this->newLine();
        $this->components->info('🔬 QA Pipeline Test');
        $this->line('─────────────────────────────────────────');

        // ── Resolve agent ───────────────────────────────────────────────
        $agentId = $this->option('agent');
        $agent = $agentId
            ? User::find($agentId)
            : User::role('Ravens Closer')->inRandomOrder()->first()
              ?? User::role('Peregrine Closer')->inRandomOrder()->first()
              ?? User::first();

        if (!$agent) {
            $this->components->error('No users found in the database.');
            return 1;
        }
        $this->line("  Agent: <fg=cyan>{$agent->name}</> ({$agent->email})");

        // ── Resolve audio file ──────────────────────────────────────────
        $audioPath = $this->argument('audio');

        if (!$audioPath) {
            // Create a quick test: use synthesized text instead
            $this->components->warn('No audio file provided. Running in TEXT-ONLY mode (skip transcription, test scoring only).');
            return $this->runTextOnlyTest($agent);
        }

        if (!file_exists($audioPath)) {
            $this->components->error("Audio file not found: {$audioPath}");
            return 1;
        }

        $fileSize = round(filesize($audioPath) / 1024 / 1024, 1);
        $this->line("  Audio: <fg=cyan>{$audioPath}</> ({$fileSize} MB)");

        // ── Step 1: Transcribe ──────────────────────────────────────────
        $engine = $this->option('engine');
        $transcript = null;

        $this->newLine();
        $this->components->task('Step 1: Transcription', function () use ($audioPath, $engine, &$transcript) {
            if ($engine === 'whisper' || $engine === 'auto') {
                try {
                    $whisper = app(WhisperService::class);
                    $transcript = $whisper->transcribe($audioPath);
                    $this->line("    Engine: <fg=green>Whisper (local, free)</>");
                    return true;
                } catch (\Throwable $e) {
                    if ($engine === 'whisper') throw $e;
                    $this->line("    Whisper failed: {$e->getMessage()}");
                }
            }

            if ($engine === 'deepgram' || ($engine === 'auto' && !$transcript)) {
                $deepgram = app(DeepgramService::class);
                $transcript = $deepgram->transcribe($audioPath);
                $this->line("    Engine: <fg=yellow>Deepgram (API, paid)</>");
                return true;
            }

            return false;
        });

        if (!$transcript) {
            $this->components->error('Transcription failed.');
            return 1;
        }

        $this->line("    Plain text: <fg=gray>" . strlen($transcript['plain']) . " chars</>");
        $this->line("    Diarized: <fg=gray>" . strlen($transcript['diarized']) . " chars</>");

        if ($this->output->isVerbose()) {
            $this->newLine();
            $this->components->info('Diarized Transcript (first 1000 chars):');
            $this->line(substr($transcript['diarized'], 0, 1000));
        }

        // ── Step 2: AI Score ────────────────────────────────────────────
        if ($this->option('skip-score')) {
            $this->newLine();
            $this->components->warn('Scoring skipped (--skip-score flag).');
            $this->showTranscriptSample($transcript);
            return 0;
        }

        $scorer = $this->option('scorer');
        $aiResult = null;
        $duration = 600; // Estimate

        $this->newLine();
        $this->components->task("Step 2: AI Scoring ({$scorer})", function () use ($transcript, $scorer, &$aiResult, $duration) {
            $prompt = QAScoringPrompt::build($transcript['diarized'], $duration);

            if ($scorer === 'claude') {
                try {
                    $claude = app(ClaudeService::class);
                    $aiResult = $claude->scoreCall($prompt);
                } catch (\Throwable $e) {
                    $this->line("    Claude failed: {$e->getMessage()}");
                    $this->line("    Falling back to Gemini...");
                    $gemini = app(GeminiService::class);
                    $aiResult = $gemini->scoreCall($prompt);
                }
            } else {
                $gemini = app(GeminiService::class);
                $aiResult = $gemini->scoreCall($prompt);
            }

            return $aiResult !== null;
        });

        if (!$aiResult) {
            $this->components->error('AI scoring failed.');
            return 1;
        }

        // ── Show results ────────────────────────────────────────────────
        $this->newLine();
        $this->showScoreResults($aiResult);

        // ── Step 3: Save to DB ──────────────────────────────────────────
        if ($this->option('dry-run')) {
            $this->newLine();
            $this->components->warn('Dry run — nothing saved to database.');
            return 0;
        }

        $this->newLine();
        $this->components->task('Step 3: Saving to database', function () use ($agent, $transcript, $aiResult, $scorer) {
            $qaCall = QaCall::create([
                'zoom_call_id' => 'TEST-' . uniqid(),
                'agent_user_id' => $agent->id,
                'agent_name' => $agent->name,
                'agent_email' => $agent->email,
                'duration_seconds' => 600,
                'call_start_time' => now(),
                'processing_status' => 'completed',
                'scored_by' => $scorer,
                'transcript_plain' => $transcript['plain'],
                'transcript_diarized' => $transcript['diarized'],
            ]);

            $resultService = app(QAResultService::class);
            $resultService->saveResult($qaCall, $aiResult);

            $this->line("    QA Call ID: <fg=cyan>#{$qaCall->id}</>");
            return true;
        });

        $this->newLine();
        $this->components->info('✅ Pipeline test complete! Check the dashboard at /qa/scoring');

        return 0;
    }

    /**
     * Test scoring-only mode using a sample transcript (no audio file needed).
     */
    private function runTextOnlyTest(User $agent): int
    {
        $sampleTranscript = $this->getSampleTranscript($agent->name);

        $this->newLine();
        $this->line("  Using sample Final Expense transcript ({" . strlen($sampleTranscript) . "} chars)");

        if ($this->option('skip-score')) {
            $this->newLine();
            $this->components->info('Sample Transcript:');
            $this->line($sampleTranscript);
            return 0;
        }

        $scorer = $this->option('scorer');
        $aiResult = null;

        $this->newLine();
        $this->components->task("AI Scoring ({$scorer})", function () use ($sampleTranscript, $scorer, &$aiResult) {
            $prompt = QAScoringPrompt::build($sampleTranscript, 480);

            if ($scorer === 'claude') {
                try {
                    $aiResult = app(ClaudeService::class)->scoreCall($prompt);
                } catch (\Throwable $e) {
                    $this->line("    Claude failed: {$e->getMessage()}");
                    $this->line("    Trying Gemini...");
                    $aiResult = app(GeminiService::class)->scoreCall($prompt);
                }
            } else {
                $aiResult = app(GeminiService::class)->scoreCall($prompt);
            }

            return $aiResult !== null;
        });

        if (!$aiResult) {
            $this->components->error('AI scoring failed. Check your GEMINI_API_KEY / ANTHROPIC_API_KEY in .env');
            return 1;
        }

        $this->newLine();
        $this->showScoreResults($aiResult);

        if (!$this->option('dry-run')) {
            $qaCall = QaCall::create([
                'zoom_call_id' => 'TEST-TEXT-' . uniqid(),
                'agent_user_id' => $agent->id,
                'agent_name' => $agent->name,
                'agent_email' => $agent->email,
                'duration_seconds' => 480,
                'call_start_time' => now(),
                'processing_status' => 'completed',
                'scored_by' => $scorer,
                'transcript_plain' => $sampleTranscript,
                'transcript_diarized' => $sampleTranscript,
            ]);

            app(QAResultService::class)->saveResult($qaCall, $aiResult);
            $this->line("  Saved as QA Call <fg=cyan>#{$qaCall->id}</>");
        }

        $this->newLine();
        $this->components->info('✅ Text-only test complete! Check /qa/scoring');
        return 0;
    }

    private function showScoreResults(array $result): void
    {
        $disp = $result['disposition'] ?? 'N/A';
        $score = $result['total_score'] ?? 0;
        $color = $score >= 90 ? 'green' : ($score >= 75 ? 'blue' : ($score >= 60 ? 'yellow' : 'red'));

        $this->components->info('Score Results:');
        $this->line("  Disposition: <fg={$color};options=bold>{$disp}</>");
        $this->line("  Total Score: <fg={$color};options=bold>{$score}/100</>");
        $this->line("  Compliance:  " . ($result['compliance_pass'] ?? false ? '<fg=green>PASS</>' : '<fg=red>FAIL</>'));

        if (!empty($result['scores'])) {
            $this->newLine();
            $this->line('  Category Scores:');
            $categories = ['opening', 'discovery', 'presentation', 'objection_handling', 'closing', 'soft_skills', 'call_control'];
            foreach ($categories as $cat) {
                $v = $result['scores'][$cat] ?? 0;
                $bar = str_repeat('█', $v) . str_repeat('░', 10 - $v);
                $catColor = $v >= 8 ? 'green' : ($v >= 6 ? 'blue' : ($v >= 4 ? 'yellow' : 'red'));
                $label = str_pad(ucwords(str_replace('_', ' ', $cat)), 20);
                $this->line("    {$label} <fg={$catColor}>{$bar}</> {$v}/10");
            }
        }

        if (!empty($result['coaching_notes'])) {
            $this->newLine();
            $this->line("  <fg=yellow>Coaching:</> {$result['coaching_notes']}");
        }

        if (!empty($result['top_issue'])) {
            $this->line("  <fg=red>Top Issue:</> {$result['top_issue']}");
        }
    }

    private function showTranscriptSample(array $transcript): void
    {
        $this->newLine();
        $this->components->info('Diarized Transcript (first 500 chars):');
        $this->line(substr($transcript['diarized'], 0, 500));
    }

    /**
     * Realistic Final Expense outbound call sample transcript.
     */
    private function getSampleTranscript(string $agentName): string
    {
        return <<<TRANSCRIPT
AGENT: This call is being recorded for quality assurance purposes. Good afternoon, my name is {$agentName} and I'm calling from Taurus Insurance Services. Am I speaking with Margaret Johnson?
CUSTOMER: Yes, this is Margaret.
AGENT: Hi Margaret! Thank you for taking my call. I'm reaching out because you recently expressed interest in information about a final expense life insurance plan. Do you remember filling out that card?
CUSTOMER: Oh yes, I think I did fill something out at the grocery store.
AGENT: Perfect. Well Margaret, what we offer is a whole life insurance policy, not a government program, but a private plan through Mutual of Omaha. It's specifically designed to cover end-of-life expenses like funeral costs, medical bills, and any outstanding debts, so your family doesn't have to worry about those expenses.
CUSTOMER: That sounds good. How much would it cost me?
AGENT: Great question. Before I can give you exact numbers, I need to ask you a few health questions to see which plan you qualify for. Are you currently taking any medications?
CUSTOMER: I take blood pressure medicine and something for my cholesterol.
AGENT: Okay, that's very common and shouldn't be a problem. Have you been hospitalized in the last two years?
CUSTOMER: No, I haven't.
AGENT: Wonderful. Have you ever been diagnosed with cancer, COPD, congestive heart failure, or kidney disease?
CUSTOMER: No, none of those.
AGENT: That's great news Margaret. Based on what you've told me, you would qualify for our preferred plan. Now the coverage amount starts at five thousand dollars and goes up to twenty-five thousand. Most of my clients go with the ten thousand dollar plan. At your age of 72, the monthly premium for ten thousand in coverage would be sixty-seven dollars and fifty cents per month.
CUSTOMER: Hmm, that's a bit more than I expected.
AGENT: I completely understand, Margaret. Let me explain what makes this valuable. There's a two-year waiting period on the full benefit, but from day one, if something were to happen, your beneficiary would receive all premiums paid back plus ten percent. After two years, they'd get the full ten thousand dollars. And this rate is locked in - it never goes up, and your coverage never goes down.
CUSTOMER: The rate never goes up? That's nice.
AGENT: Exactly. Now, who would you like as your beneficiary? Who would you want to receive the benefit?
CUSTOMER: That would be my daughter, Sarah Johnson.
AGENT: Perfect. And Margaret, do I have your verbal consent to proceed with this application today?
CUSTOMER: Yes, let's go ahead with it.
AGENT: Wonderful! Let me get your information to complete the application...
TRANSCRIPT;
    }
}
