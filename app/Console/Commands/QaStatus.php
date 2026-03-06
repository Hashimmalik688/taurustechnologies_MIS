<?php

namespace App\Console\Commands;

use App\Models\QA\QaCall;
use Illuminate\Console\Command;

class QaStatus extends Command
{
    protected $signature = 'qa:status {--limit=20 : Number of recent calls to show}';
    protected $description = 'Show the current status of QA pipeline jobs';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $this->newLine();
        $this->info('QA Pipeline Status');
        $this->line(str_repeat('─', 80));

        // Summary counts
        $counts = QaCall::selectRaw('processing_status, count(*) as cnt')
            ->groupBy('processing_status')
            ->pluck('cnt', 'processing_status');

        $this->table(['Status', 'Count'], $counts->map(fn($v, $k) => [$k, $v])->values()->toArray());

        // Recent calls
        $this->newLine();
        $this->line("Recent {$limit} calls:");

        $calls = QaCall::with('result')
            ->latest()
            ->limit($limit)
            ->get(['id','zoom_call_id','agent_name','caller_number','callee_number',
                   'duration_seconds','processing_status','failure_reason','created_at']);

        $rows = $calls->map(function ($c) {
            $min    = round($c->duration_seconds / 60, 1);
            $score  = $c->result?->total_score ?? '-';
            $disp   = $c->result?->disposition ?? '-';
            $status = $c->processing_status;
            if ($status === 'failed' && $c->failure_reason) {
                $status .= ' — ' . substr($c->failure_reason, 0, 40);
            }
            return [
                $c->id,
                substr($c->zoom_call_id, 0, 22),
                $c->agent_name ?? '?',
                $c->callee_number ?? $c->caller_number ?? '?',
                "{$min}m",
                $status,
                $score !== '-' ? "{$score}%" : '-',
                $disp,
            ];
        })->toArray();

        $this->table(
            ['ID', 'Zoom Call ID', 'Agent', 'Number', 'Dur', 'Status', 'Score', 'Disposition'],
            $rows
        );

        $this->newLine();
        return 0;
    }
}
