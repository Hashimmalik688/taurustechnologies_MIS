<?php
/**
 * Recalculates total_score for QaResults that have total_score=0
 * but have valid sub-scores. Uses the standard formula:
 *
 * total_score = round((S1+S2+S3+S4+S5+S6+S7) / 70 * 100)
 *
 * Compliance is a hard disposition gate, not a score component.
 * A compliance-fail call can still have a meaningful numeric score.
 */
chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\QA\QaResult;

echo "=== Recalculate total_score for QaResults with score=0 but sub-scores present ===\n\n";

$results = QaResult::where('total_score', 0)
    ->whereNotNull('score_opening')
    ->get();

$fixed = 0;
foreach ($results as $r) {
    $sum = $r->score_opening
         + $r->score_discovery
         + $r->score_presentation
         + $r->score_objection_handling
         + $r->score_closing
         + $r->score_soft_skills
         + $r->score_call_control;

    if ($sum <= 0) {
        echo "QaResult #{$r->id} (QaCall #{$r->qa_call_id}): sub-scores all zero, skipping\n";
        continue;
    }

    $newScore = round($sum / 70 * 100, 2);

    $r->total_score = $newScore;
    $r->save();

    echo "QaResult #{$r->id} (QaCall #{$r->qa_call_id}): {$r->disposition} | sub-sum={$sum} → total_score={$newScore}\n";
    $fixed++;
}

echo "\n=== Done: fixed {$fixed} / {$results->count()} records ===\n";

