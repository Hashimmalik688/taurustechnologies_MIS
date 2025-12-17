# Attendance Time Restrictions

## Summary of Changes

The attendance system now enforces strict time windows for check-in and includes automatic checkout functionality.

## ✅ What's Implemented

### 1. Check-in Time Window Enforcement
- **Allowed**: 6:00 PM (18:00) to 6:00 AM (06:00)
- **Blocked**: 6:00 AM (06:00) to 6:00 PM (18:00)
- Provides 1-hour buffer before (6 PM) and after (6 AM) core office hours (7 PM - 5 AM)

### 2. Automatic Checkout
- Runs daily at **6:10 AM** via scheduled command
- Automatically checks out employees who didn't manually check out
- Sets checkout time to **6:00 AM** (end of allowed window)
- Marks records with `auto_checkout = true` flag

### 3. Error Messages
When attempting to check in outside allowed hours:
> "Attendance can only be marked between 6:00 PM and 6:00 AM. Office hours are 7:00 PM to 5:00 AM with 1-hour buffer."

## 📁 Files Modified

### 1. `app/Services/AttendanceService.php`
- Added time window validation in `markAttendance()`
- Added time window validation in `checkAndMarkDailyAttendance()`
- Added new method: `autoCheckoutOverdueAttendances()`

### 2. `app/Models/Attendance.php`
- Added `auto_checkout` to fillable array
- Added `auto_checkout` to casts (boolean)

### 3. `app/Console/Commands/AutoCheckoutAttendance.php` (NEW)
- Created command: `attendance:auto-checkout`
- Calls `AttendanceService::autoCheckoutOverdueAttendances()`

### 4. `app/Console/Kernel.php`
- Registered `AutoCheckoutAttendance` command
- Scheduled to run daily at 6:10 AM (Asia/Karachi timezone)

### 5. Database Migration
- `2025_12_17_045110_add_auto_checkout_to_attendances_table.php`
- Added `auto_checkout` boolean column (default: false)

## 🚀 How It Works

### Check-in Flow
```
Employee attempts to mark attendance
    ↓
System checks current time
    ↓
Is time between 6 PM and 6 AM?
    ↓ YES                    ↓ NO
Allow check-in           Block with error message
```

### Auto-checkout Flow
```
6:10 AM daily (scheduled)
    ↓
Find all attendance records from previous shift
    ↓
Filter: date = yesterday AND logout_time IS NULL
    ↓
For each record:
  - Set logout_time = 6:00 AM
  - Set auto_checkout = true
    ↓
Log: "Checked out {count} employee(s)"
```

## 🧪 Testing

### Manual Command Execution
```bash
php artisan attendance:auto-checkout
```

**Note**: Will only work between 6:00 AM - 6:30 AM. Outside this window, returns:
> "Auto-checkout only runs between 6:00 AM and 6:30 AM."

### View Scheduled Tasks
```bash
php artisan schedule:list
```

Should show:
```
10 6 * * * php artisan attendance:auto-checkout ... Next Due: X hours from now
```

### Check Auto-checkout Records
Query database to see auto-checkout flag:
```sql
SELECT user_id, date, login_time, logout_time, auto_checkout 
FROM attendances 
WHERE auto_checkout = 1;
```

## 📊 Time Window Examples

| Time | Day | Check-in Allowed? | Reason |
|------|-----|-------------------|--------|
| 5:59 PM | Wed | ❌ No | Before 6 PM window |
| 6:00 PM | Wed | ✅ Yes | Start of allowed window |
| 7:00 PM | Wed | ✅ Yes | Core office hours (on-time) |
| 7:20 PM | Wed | ✅ Yes | Core office hours (late) |
| 11:30 PM | Wed | ✅ Yes | Mid-shift |
| 2:00 AM | Thu | ✅ Yes | Night shift (marks for Wed) |
| 5:00 AM | Thu | ✅ Yes | Shift end time |
| 5:59 AM | Thu | ✅ Yes | Within buffer |
| 6:00 AM | Thu | ✅ Yes | Last minute of buffer |
| 6:01 AM | Thu | ❌ No | After buffer expires |
| 12:00 PM | Thu | ❌ No | Outside office hours |

## 🔧 Production Setup

### 1. Ensure Laravel Scheduler is Running
Add to crontab (Linux) or Task Scheduler (Windows):
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Monitor Auto-checkout Logs
Check Laravel logs for auto-checkout execution:
```bash
tail -f storage/logs/laravel.log | grep "auto-checkout"
```

### 3. Verify Timezone
Ensure `config/app.php` has correct timezone:
```php
'timezone' => 'Asia/Karachi',
```

## 🔒 Force Override (Admin Only)

The `markAttendance()` method accepts a `$forceOffice` parameter to bypass:
- Office network check
- **Does NOT bypass time window restrictions** (security feature)

To allow admin override of time restrictions, modify `markAttendance()` to also check `$forceOffice` for time window validation.

## 📝 Notes

- Auto-checkout only modifies records from **previous day's shift**
- Employees checked in after 5 AM are on **current day's shift** and won't be auto-checked out until next day
- Auto-checkout window (6:00 AM - 6:30 AM) prevents duplicate processing if command runs multiple times
- The `auto_checkout` flag helps identify system-generated vs manual checkouts for reporting
