# Night Shift Attendance System

## Overview
The attendance system has been updated to properly handle night shift operations (7 PM to 5 AM) with enforced check-in windows and automatic checkout.

## Office Hours & Check-in Window
- **Core Office Hours**: 7:00 PM - 5:00 AM (night shift)
- **Allowed Check-in Window**: 6:00 PM - 6:00 AM (1-hour buffer on each side)
- **Check-in Blocked**: 6:00 AM - 6:00 PM (outside office hours)
- **Auto-checkout**: 6:10 AM daily for any employee who hasn't checked out

## Key Features

### 1. Check-in Time Restrictions
Employees can only mark attendance between **6:00 PM and 6:00 AM**. Attempts to check in outside this window will be blocked with the message:
> "Attendance can only be marked between 6:00 PM and 6:00 AM. Office hours are 7:00 PM to 5:00 AM with 1-hour buffer."

### 2. Automatic Checkout
At **6:10 AM daily**, the system automatically checks out all employees who:
- Have an active attendance record from the previous shift
- Have not manually checked out
- Checkout time is set to **6:00 AM** (shift end time + 1-hour buffer)
- Record is marked with `auto_checkout = true` flag

### 3. Shift Date Logic
For a night shift that spans midnight (7 PM - 5 AM):
- **Before 5:00 AM**: Attendance is recorded for the **previous day's shift**
- **After 5:00 AM**: Attendance is recorded for the **current day's shift**

### 3. Shift Date Logic
For a night shift that spans midnight (7 PM - 5 AM):
- **Before 5:00 AM**: Attendance is recorded for the **previous day's shift**
- **After 5:00 AM**: Attendance is recorded for the **current day's shift**

## Implementation Details

### Database Changes
Added `auto_checkout` boolean column to `attendances` table:
```php
$table->boolean('auto_checkout')->default(false);
```

### Updated Methods in AttendanceService

1. **`markAttendance()`** - Main attendance marking
   - Validates check-in time is between 6 PM and 6 AM
   - Weekend check uses shift date
   - Attendance record created with shift date
   - Duplicate check uses shift date

2. **`markLogout()`** - Logout time recording
   - Finds attendance record by shift date

3. **`checkAndMarkDailyAttendance()`** - Auto-attendance on dashboard
   - Validates time window (6 PM - 6 AM)
   - Checks for existing attendance by shift date

4. **`autoCheckoutOverdueAttendances()`** - NEW method for auto-checkout
   - Runs daily at 6:10 AM via scheduled command
   - Checks out all employees from previous shift without logout
   - Sets logout time to 6:00 AM
   - Marks record with `auto_checkout = true`

### Scheduled Commands

Added to `app/Console/Kernel.php`:
```php
// Auto-checkout employees at 6:10 AM who haven't checked out
$schedule->command('attendance:auto-checkout')
    ->dailyAt('06:10')
    ->timezone('Asia/Karachi');
```

Manual execution: `php artisan attendance:auto-checkout`

## Examples

### Scenario 1: Evening Check-in (Within Window)
- Current time: **8:30 PM (20:30)** on Wednesday
- Check-in allowed: ✅ Yes (within 6 PM - 6 AM window)
- Shift date: **Wednesday** (same day, after 5 AM)
- Attendance recorded for: **Wednesday**

### Scenario 2: Late Night Check-in (Within Window)
- Current time: **11:45 PM (23:45)** on Wednesday
- Check-in allowed: ✅ Yes (within 6 PM - 6 AM window)
- Shift date: **Wednesday** (same day, after 5 AM)
- Attendance recorded for: **Wednesday**

### Scenario 3: Early Morning Check-in (Within Window)
- Current time: **2:30 AM (02:30)** on Thursday
- Check-in allowed: ✅ Yes (within 6 PM - 6 AM window)
- Shift date: **Wednesday** (previous day, before 5 AM)
- Attendance recorded for: **Wednesday** (the shift that started Wednesday evening)

### Scenario 4: End of Buffer Check-in (Last Minute)
- Current time: **5:55 AM (05:55)** on Thursday
- Check-in allowed: ✅ Yes (within 6 PM - 6 AM window)
- Shift date: **Thursday** (current day, after 5 AM)
- Attendance recorded for: **Thursday**

### Scenario 5: After Buffer Check-in (BLOCKED)
- Current time: **6:15 AM (06:15)** on Thursday
- Check-in allowed: ❌ No (outside 6 PM - 6 AM window)
- Error message: "Attendance can only be marked between 6:00 PM and 6:00 AM..."

### Scenario 6: Afternoon Check-in Attempt (BLOCKED)
- Current time: **2:00 PM (14:00)** on Thursday
- Check-in allowed: ❌ No (outside 6 PM - 6 AM window)
- Error message: "Attendance can only be marked between 6:00 PM and 6:00 AM..."

### Scenario 7: Shift End Checkout
- Current time: **4:50 AM (04:50)** on Thursday
- Shift date: **Wednesday** (previous day, before 5 AM)
- Logout recorded for: **Wednesday's attendance record**

### Scenario 8: Auto-checkout Scenario
- Employee checked in: **7:30 PM Wednesday**
- Employee forgot to checkout
- Auto-checkout runs: **6:10 AM Thursday**
- System sets logout time: **6:00 AM Thursday**
- Record marked: `auto_checkout = true`
- Attendance recorded for: **Wednesday's shift** (completed)

## Status Calculation
The system already handles night shift status correctly:
- **On-time**: Arrival between 7:00 PM - 7:15 PM OR any time before 5:00 AM
- **Late**: Arrival after 7:15 PM (same calendar day)

## Testing Recommendations

### Critical Time Points to Test

#### Check-in Validation Tests:
1. **5:59 PM** - Should be BLOCKED (before 6 PM window)
2. **6:00 PM** - Should be ALLOWED (start of window)
3. **7:00 PM** - Should be ALLOWED (on-time, core hours)
4. **7:20 PM** - Should be ALLOWED (late status, but within window)
5. **11:30 PM** - Should be ALLOWED (mid-shift)
6. **2:00 AM** - Should be ALLOWED (night shift, marks for previous day)
7. **4:59 AM** - Should be ALLOWED (before 5 AM, marks for previous day)
8. **5:00 AM** - Should be ALLOWED (shift end time)
9. **5:59 AM** - Should be ALLOWED (within buffer)
10. **6:00 AM** - Should be ALLOWED (last minute of buffer)
11. **6:01 AM** - Should be BLOCKED (after buffer)
12. **12:00 PM** - Should be BLOCKED (outside hours)

#### Auto-checkout Tests:
1. Create attendance at 7 PM without logout
2. Run command at 6:10 AM next day
3. Verify logout_time set to 6:00 AM
4. Verify auto_checkout flag is true

#### Shift Date Tests:
1. **4:59 AM Thursday** - Should mark for Wednesday
2. **5:00 AM Thursday** - Should mark for Thursday

### Manual Testing Commands
```bash
# Test auto-checkout manually
php artisan attendance:auto-checkout

# Check scheduled tasks
php artisan schedule:list

# Run scheduler (in production)
php artisan schedule:work
```

## Weekend Handling
Weekend checks now use shift date:
- If marking attendance at 2 AM on Monday, it checks if Sunday (previous day) is a weekend
- If marking attendance at 8 PM on Friday, it checks if Friday is a weekend
