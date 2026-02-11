# Fix Ravens Lead Visibility Issues

## Problem
Manually created lead "Hashim Shabbir" (or other leads) not showing in Ravens calling dashboard.

## Root Causes

The Ravens calling dashboard (`/ravens/calling`) filters leads based on several criteria in `RavensDashboardController::calling()`:

1. **verified_by field**: Leads with `verified_by` set are excluded (line 155)
2. **Team = 'peregrine'**: Peregrine team leads are excluded
3. **Missing phone number**: Leads must have valid phone numbers
4. **Sold leads**: Leads marked as sold (status='accepted' and sale_at set) only show to the closer who sold them

## Solutions Applied

### 1. ✅ Updated CreateLead Component
**File**: `app/Livewire/CreateLead.php`

Added explicit code to prevent `verified_by` from being set on manual lead creation:

```php
// Ensure verified_by is NOT set for manually created leads
// This ensures the lead shows up in Ravens calling list
$leadData['verified_by'] = null;
```

This fixes the issue for **future** manually created leads.

### 2. ✅ Multiple Beneficiaries Support
**Files**: 
- `app/Livewire/CreateLead.php`
- `resources/views/livewire/create-lead.blade.php`

Added support for multiple beneficiaries with:
- Dynamic add/remove beneficiary UI
- Relation dropdown (Spouse, Child, Parent, etc.)
- Stores data in `beneficiaries` JSON field
- Backwards compatible with single `beneficiary` field

### 3. ✅ Removed Card Info Masking
**File**: `resources/views/admin/leads/show.blade.php`

Changed from:
```blade
{{ $insurance->card_number ? '****  ****  ****  ' . substr($insurance->card_number, -4) : 'Not provided' }}
{{ $insurance->cvv ? '***' : 'Not provided' }}
```

To:
```blade
{{ $insurance->card_number ?? 'Not provided' }}
{{ $insurance->cvv ?? 'Not provided' }}
```

**⚠️ Security Note**: This removes PCI-DSS recommended masking. Consider restricting view access or reverting if compliance is needed.

## Fix Existing Lead (Hashim Shabbir)

### Option 1: Using Fix Script (Recommended)

Run the automated fix script:

```bash
cd /var/www/taurus-crm
php fix_hashim_lead.php
```

The script will:
1. Find the "Hashim Shabbir" lead
2. Diagnose why it's not showing in Ravens calling
3. Offer to fix the issues automatically

### Option 2: Using Laravel Tinker

```bash
php artisan tinker
```

Then run:

```php
// Find and fix the lead
$lead = Lead::where('cn_name', 'like', '%Hashim Shabbir%')->first();

if ($lead) {
    echo "Found: {$lead->cn_name} (ID: {$lead->id})\n";
    echo "Status: {$lead->status}\n";
    echo "verified_by: " . ($lead->verified_by ?? 'NULL') . "\n";
    echo "team: " . ($lead->team ?? 'NULL') . "\n";
    echo "phone: {$lead->phone_number}\n\n";
    
    // Fix the issues
    $lead->verified_by = null;
    $lead->team = null; // Remove if set to 'peregrine'
    $lead->save();
    
    echo "✅ Fixed! Lead should now appear in Ravens calling.\n";
} else {
    echo "❌ Lead not found.\n";
}
```

### Option 3: Using SQL Query

Connect to MySQL:

```bash
mysql -u your_user -p taurus_crm
```

Run:

```sql
-- Find the lead
SELECT id, cn_name, phone_number, verified_by, team, status, created_at 
FROM leads 
WHERE cn_name LIKE '%Hashim Shabbir%';

-- Fix the lead (replace ID with actual ID from above)
UPDATE leads 
SET verified_by = NULL, team = NULL 
WHERE id = YOUR_LEAD_ID;
```

## Verification

After applying the fix:

1. Go to Ravens calling dashboard: `/ravens/calling`
2. Look for "Hashim Shabbir" in the leads list
3. The lead should now appear if:
   - Has valid phone number
   - `verified_by` is NULL
   - `team` is not 'peregrine'
   - Not sold (or you are the closer who sold it)

## Prevention

All **new** manually created leads will automatically work correctly because the CreateLead component now explicitly sets `verified_by = null`.

## Troubleshooting

If lead still doesn't appear, check:

```bash
php artisan tinker
```

```php
$lead = Lead::where('cn_name', 'like', '%Hashim Shabbir%')->first();

// Check all relevant fields
echo "verified_by: " . ($lead->verified_by ?? 'NULL') . "\n";
echo "team: " . ($lead->team ?? 'NULL') . "\n";
echo "phone_number: " . ($lead->phone_number ?? 'NULL') . "\n";
echo "status: {$lead->status}\n";
echo "sale_at: " . ($lead->sale_at ?? 'NULL') . "\n";
echo "closer_name: " . ($lead->closer_name ?? 'NULL') . "\n";
```

### Common Issues:

1. **Phone number missing**: Lead requires valid phone_number
2. **Status = 'accepted' + sale_at set**: Only visible to assigned closer
3. **Team = 'peregrine'**: Ravens excludes Peregrine team leads
4. **Cache issue**: Try `php artisan cache:clear`

## Files Modified

1. `app/Livewire/CreateLead.php` - Added beneficiaries support, fixed verified_by
2. `resources/views/livewire/create-lead.blade.php` - Multiple beneficiaries UI
3. `resources/views/admin/leads/show.blade.php` - Removed card masking
4. `fix_hashim_lead.php` - Automated fix script (new file)
5. `FIX_RAVENS_LEAD_VISIBILITY.md` - This documentation (new file)

## Related Files

- `app/Http/Controllers/Admin/RavensDashboardController.php` (line 125-157) - Ravens calling filter logic
- `app/Models/Lead.php` - Lead model with beneficiaries JSON support
- `database/migrations/2026_01_02_021649_add_beneficiaries_json_to_leads_table.php` - Beneficiaries migration
