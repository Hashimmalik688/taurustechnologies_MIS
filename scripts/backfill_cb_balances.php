<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = app(App\Services\CarrierSheetService::class);
$fixed = 0;

App\Models\CarrierSheetEntry::withoutTrashed()
    ->where('status', 'chargeback')
    ->where('paid_amount', '>', 0)
    ->get()
    ->each(function($entry) use ($service, &$fixed) {
        $entry->setRelation('carrierRate', $entry->carrierRate);
        $service->recalculateEntry($entry);
        $entry->save();
        $fixed++;
    });

echo "Recalculated: {$fixed} chargeback entries\n";
