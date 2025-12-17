<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('email', 'admin@taurus.com')->first();

if ($user) {
    $user->password = \Illuminate\Support\Facades\Hash::make('12345678');
    $user->save();
    echo "✅ Password reset successfully!\n";
    echo "Email: admin@taurus.com\n";
    echo "Password: 12345678\n";
    echo "\nPassword hash: " . $user->password . "\n";
} else {
    echo "❌ User not found!\n";
}
