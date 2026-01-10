# Bulk Lead Import - Configuration & Limits

## Current Issues
Your 1-2MB Excel file with 3000 leads fails during import due to server limits.

## Limits Found
| Setting | Current Value | Needed for 3000+ leads |
|---------|---------------|------------------------|
| Upload Max Filesize | 2M | 50M+ |
| POST Max Size | 8M | 50M+ |
| Max Execution Time | Unlimited | ✅ Good |
| Memory Limit | Unlimited | ✅ Good |

## Fix Required (1 Step)

### Update PHP Configuration
Edit `/etc/php/8.2/fpm/php.ini` (or your PHP version):

```ini
# Find these lines and update:
upload_max_filesize = 50M    # Changed from 2M
post_max_size = 50M          # Changed from 8M
```

Then restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

## Code Update (Already Applied)
✅ Updated `app/Http/Controllers/Admin/LeadController.php` - now accepts up to 50MB files

## Testing Import
After PHP restart, try importing:
- **File size**: Up to 50MB
- **Rows**: 3000+ leads
- **Format**: Excel (.xlsx, .xls) or CSV

## How Import Works
1. File uploaded and stored temporarily
2. LeadsImport class processes rows one-by-one
3. For each row:
   - Validates phone number (required)
   - Checks if lead already exists
   - Creates new lead or adds carrier to existing
   - Continues on errors (doesn't stop import)
4. Returns count of created leads

## Tips for Large Imports
- **Test first**: Import 100 leads to verify column headers
- **Monitor memory**: Watch browser console for timeout messages
- **Batch imports**: Consider splitting 10,000+ leads into multiple 5,000-lead files
- **Check logs**: View `storage/logs/laravel.log` for detailed import errors

## Support Columns (Flexible Matching)
Headers are auto-normalized (case-insensitive, spaces/symbols converted):
- "Phone Number" = "phone_number" = "PHONE NUMBER" ✅
- "DOB" = "Date Of Birth" = "dob" ✅
- Supported: 50+ lead fields including carrier, coverage, beneficiary, bank details, etc.

## Troubleshooting
| Error | Solution |
|-------|----------|
| File too large | Check PHP limits above |
| Import hangs/times out | Check server logs: `tail -f storage/logs/laravel.log` |
| Leads not appearing | Verify `status = 'pending'` - only closed/accepted show in All Leads |
| Phone validation fails | Ensure valid US format (10-digit or 1+10-digit) |
| Duplicate detection | Exact phone match prevents duplicates |
