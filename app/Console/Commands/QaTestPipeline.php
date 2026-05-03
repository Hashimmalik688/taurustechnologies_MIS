<?php

namespace App\Console\Commands;

use App\Models\QA\QaCall;
use App\Models\User;
use App\Services\QA\ClaudeService;
use App\Services\QA\GeminiService;
use App\Services\QA\QAResultService;
use App\Services\QA\QAScoringPrompt;
use Illuminate\Console\Command;

class QaTestPipeline extends Command
{
    protected $signature = 'qa:test
        {--agent= : User ID of the agent (default: first available closer)}
        {--scorer=claude : AI scorer: claude or gemini}
        {--dry-run : Show what would happen without saving to DB}';

    protected $description = 'Test the QA pipeline (text-only mode): score a sample transcript with Claude/Gemini and save to DB.';

    public function handle(): int
    {
        $this->newLine();
        $this->components->info('🔬 QA Pipeline Test (text-only mode)');
        $this->line('─────────────────────────────────────────');
        $this->line('  Transcription is handled via file upload in the QA scoring page (AssemblyAI).');
        $this->line('  This command tests AI scoring only using a built-in sample transcript.');

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

        return $this->runTextOnlyTest($agent);
    }

    private function runTextOnlyTest(User $agent): int
    {
        $sampleTranscript = $this->getSampleTranscript($agent->name);

        $this->newLine();
        $this->line("  Using sample Final Expense transcript (" . strlen($sampleTranscript) . " chars)");

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
                'agent_name'    => $agent->name,
                'agent_email'   => $agent->email,
                'duration_seconds' => 480,
                'call_start_time'  => now(),
                'processing_status' => 'completed',
                'scored_by' => $scorer,
                'transcript_plain'    => $sampleTranscript,
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
        $disp   = $result['disposition'] ?? 'N/A';
        $score  = $result['total_score'] ?? 0;
        $color  = $score >= 90 ? 'green' : ($score >= 75 ? 'blue' : ($score >= 60 ? 'yellow' : 'red'));

        $this->components->info('Score Results:');
        $this->line("  Disposition: <fg={$color};options=bold>{$disp}</>");
        $this->line("  Total Score: <fg={$color};options=bold>{$score}/100</>");
        $this->line("  Compliance:  " . ($result['compliance_pass'] ?? false ? '<fg=green>PASS</>' : '<fg=red>FAIL</>'));

        if (!empty($result['scores'])) {
            $this->newLine();
            $this->line('  Category Scores:');
            $categories = ['opening', 'discovery', 'presentation', 'objection_handling', 'closing', 'soft_skills', 'call_control'];
            foreach ($categories as $cat) {
                $v     = $result['scores'][$cat] ?? 0;
                $bar   = str_repeat('█', $v) . str_repeat('░', 10 - $v);
                $c     = $v >= 8 ? 'green' : ($v >= 6 ? 'blue' : ($v >= 4 ? 'yellow' : 'red'));
                $label = str_pad(ucwords(str_replace('_', ' ', $cat)), 20);
                $this->line("    {$label} <fg={$c}>{$bar}</> {$v}/10");
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
