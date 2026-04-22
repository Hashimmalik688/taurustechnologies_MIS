<?php
// One-time script to re-sync timed-out sales to Google Sheets
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ids = [9188, 9189]; // 9187 (Deana) and 2123 (Andrew) already in sheet
$svc = app(App\Services\GoogleSheetsService::class);

foreach ($ids as $id) {
    $lead = App\Models\Lead::find($id);
    if (!$lead) {
        echo "Lead #{$id} not found\n";
        continue;
    }
    echo "Syncing Lead #{$id} ({$lead->cn_name})...\n";
    $svc->appendSale($lead);
    echo "Done.\n";
}
echo "Finished.\n";
