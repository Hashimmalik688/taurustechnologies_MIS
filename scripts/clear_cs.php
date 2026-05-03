<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\Illuminate\Support\Facades\DB::statement('DELETE FROM carrier_sheet_entries');
\Illuminate\Support\Facades\DB::statement('ALTER TABLE carrier_sheet_entries AUTO_INCREMENT = 1');
$count = \Illuminate\Support\Facades\DB::table('carrier_sheet_entries')->count();
echo "Done. Remaining rows: $count\n";
