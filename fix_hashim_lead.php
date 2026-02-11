<?php

/**
 * Fix script to ensure "Hashim Shabbir" lead (and other manually created leads)
 * appear in Ravens calling dashboard by clearing verified_by field
 * 
 * Usage: php artisan tinker < fix_hashim_lead.php
 * Or run directly: php fix_hashim_lead.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Lead;

// Find the lead by name
$lead = Lead::where('cn_name', 'like', '%Hashim Shabbir%')->first();

if (!$lead) {
    echo "❌ Lead 'Hashim Shabbir' not found.\n";
    echo "Searching for similar names...\n";
    
    $similarLeads = Lead::where('cn_name', 'like', '%Hashim%')
        ->orWhere('cn_name', 'like', '%Shabbir%')
        ->get(['id', 'cn_name', 'verified_by', 'phone_number', 'status', 'created_at']);
    
    if ($similarLeads->count() > 0) {
        echo "\nFound " . $similarLeads->count() . " similar lead(s):\n";
        foreach ($similarLeads as $similarLead) {
            echo "  ID: {$similarLead->id} | Name: {$similarLead->cn_name} | Phone: {$similarLead->phone_number} | Status: {$similarLead->status} | verified_by: " . ($similarLead->verified_by ?? 'NULL') . "\n";
        }
    } else {
        echo "No similar leads found.\n";
    }
    exit(1);
}

echo "✅ Found lead: {$lead->cn_name} (ID: {$lead->id})\n";
echo "   Phone: {$lead->phone_number}\n";
echo "   Status: {$lead->status}\n";
echo "   Created: {$lead->created_at}\n";
echo "   verified_by: " . ($lead->verified_by ?? 'NULL') . "\n";
echo "   Team: " . ($lead->team ?? 'NULL') . "\n\n";

// Check why it's not showing in Ravens calling
$issues = [];

if ($lead->verified_by) {
    $issues[] = "❌ Lead has verified_by set to '{$lead->verified_by}' - this filters it out from Ravens calling";
}

if ($lead->team === 'peregrine') {
    $issues[] = "❌ Lead is marked as 'peregrine' team - Ravens calling excludes Peregrine leads";
}

if (!$lead->phone_number || $lead->phone_number === 'N/A' || trim($lead->phone_number) === '') {
    $issues[] = "❌ Lead has no valid phone number - required for Ravens calling system";
}

if ($lead->status === 'accepted' && $lead->sale_at) {
    $issues[] = "⚠️ Lead is marked as sold (accepted + sale_at set) - only visible to the closer who sold it";
}

if (empty($issues)) {
    echo "✅ No issues found. Lead should appear in Ravens calling dashboard.\n";
    exit(0);
}

echo "Issues preventing lead from showing in Ravens calling:\n";
foreach ($issues as $issue) {
    echo "  {$issue}\n";
}

echo "\nDo you want to fix these issues? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if (strtolower($line) !== 'yes' && strtolower($line) !== 'y') {
    echo "Aborted. No changes made.\n";
    exit(0);
}

// Apply fixes
echo "\nApplying fixes...\n";

$updated = false;

if ($lead->verified_by) {
    echo "  → Clearing verified_by field...\n";
    $lead->verified_by = null;
    $updated = true;
}

if ($lead->team === 'peregrine') {
    echo "  → Clearing peregrine team assignment...\n";
    $lead->team = null;
    $updated = true;
}

if ($updated) {
    $lead->save();
    echo "\n✅ Lead fixed successfully! It should now appear in Ravens calling dashboard.\n";
    echo "   Refresh the Ravens calling page to see the lead.\n";
} else {
    echo "\n✅ No fixable issues. Please check:\n";
    echo "   - Phone number: {$lead->phone_number}\n";
    echo "   - Status: {$lead->status}\n";
    echo "   - Sale status: " . ($lead->sale_at ? 'Sold' : 'Not sold') . "\n";
}
