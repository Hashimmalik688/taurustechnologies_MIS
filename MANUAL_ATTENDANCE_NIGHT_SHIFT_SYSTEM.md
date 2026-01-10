# Manual Attendance Entry System - Night Shift Edition

## Overview
This system replaces the old manual attendance entry logic with a new implementation that correctly handles night shifts (shifts that span midnight).

## Key Features

### 1. ✅ Automatic Schema Discovery
- **No Hardcoded Field Names**: The system automatically discovers column names from your `attendances` table
- **Flexible Mapping**: Supports both `user_id` and `employee_id` naming conventions
- **Uses Existing Schema**: Works with your current table structure without modifications

### 2. 🌙 Night Shift Logic
**The Rule**: If a shift starts in the evening and ends the next morning, it's saved as ONE record assigned to the shift START date.

**Example**:
```
Input:
  Date: 2026-01-05
  Login Time: 22:00
  Logout Time: 06:00

Storage:
  date: 2026-01-05 (shift started on the 5th)
  login_time: 2026-01-05 22:00:00
  logout_time: 2026-01-06 06:00:00  ← Next day automatically detected!
  working_hours: 8.0 (auto-calculated)
```

**Detection Logic**:
- If `logout_time` is BEFORE `login_time` (hour-wise), the system adds 1 day to logout
- Example: 22:00 → 06:00 means logout is next morning
- Duration is calculated correctly: 8 hours (not -16 hours)

### 3. 🔄 Instant Dashboard Sync (UpdateOrCreate)
The system uses Laravel's `updateOrCreate()` method:

```php
Attendance::updateOrCreate(
    ['user_id' => $employeeId, 'date' => $shiftDate],  // Find existing record
    [/* all attendance data */]                         // Update or create
);
```

**What This Means**:
- If an employee has an "Absent" record for Jan 5th
- Admin creates manual entry for Jan 5th with "Present"
- The "Absent" record is **OVERWRITTEN** with "Present"
- Employee's dashboard shows "Present" immediately after page reload
- No duplicate records (unique constraint: `user_id` + `date`)

### 4. 📊 Working Hours Auto-Calculation
The `Attendance` model's `boot()` method automatically calculates working hours:

```php
protected static function boot() {
    static::saving(function ($attendance) {
        if ($attendance->login_time && $attendance->logout_time) {
            // Parse times
            $loginTime = Carbon::parse($attendance->login_time);
            $logoutTime = Carbon::parse($attendance->logout_time);
            
            // Handle overnight: if logout < login, add 1 day
            if ($logoutTime->lt($loginTime)) {
                $logoutTime->addDay();
            }
            
            // Calculate hours
            $attendance->working_hours = round($loginTime->diffInHours($logoutTime, true), 1);
        }
    });
}
```

**No manual calculation needed!** Just provide login and logout times.

## Implementation Details

### Backend: AttendanceController@markManual

**Location**: `app/Http/Controllers/Admin/AttendanceController.php`

**Key Changes**:
1. ❌ **REMOVED**: Old `force_office` logic (not needed for manual entries)
2. ❌ **REMOVED**: Direct date string concatenation
3. ✅ **ADDED**: Automatic field discovery from model
4. ✅ **ADDED**: Night shift detection and date adjustment
5. ✅ **ADDED**: Comprehensive logging with overnight shift indicators
6. ✅ **ADDED**: Response includes shift duration and overnight flag

**Request Format**:
```json
{
    "user_id": 123,
    "date": "2026-01-05",
    "login_time": "22:00",
    "logout_time": "06:00",
    "status": "present"
}
```

**Response Format**:
```json
{
    "success": true,
    "message": "Attendance created successfully",
    "attendance": {
        "id": 456,
        "date": "2026-01-05",
        "login_time": "22:00",
        "logout_time": "06:00",
        "duration_hours": 8.0,
        "is_overnight": true
    }
}
```

### Frontend: Manual Entry Modal

**Location**: `resources/views/admin/attendance/index.blade.php`

**Enhancements**:
1. **Real-time Overnight Detection**
   - Detects when login is PM and logout is AM
   - Shows alert with calculated duration
   - Updates as user types times

2. **Visual Feedback**
   ```html
   <div class="alert alert-info">
       Overnight Shift Detected!
       Calculated Duration: 8h 0m (22:00 → 06:00 next day)
   </div>
   ```

3. **Success Message**
   - Shows if shift is overnight
   - Displays calculated duration
   - Confirms which date it's saved to

### Database Schema

**Discovered Automatically From**:
```
attendances table
├── id (primary key)
├── user_id (foreign key → users.id)
├── date (date - shift start date)
├── login_time (datetime)
├── logout_time (datetime, nullable)
├── status (string: present/late/absent)
├── working_hours (integer, auto-calculated)
├── ip_address (string)
├── auto_checkout (boolean)
└── timestamps
```

**Unique Constraint**: `(user_id, date)` - prevents duplicate records

## Testing the System

### Verification Command
Run the schema verification command:
```bash
php artisan attendance:verify-schema
```

This will:
- Display all columns in attendances table
- Show column types and constraints
- Verify required fields exist
- Show overnight shift example

### Manual Entry Test Cases

#### Test 1: Normal Day Shift
```
Date: 2026-01-05
Login: 09:00
Logout: 17:00
Expected Result:
  - Saved to: 2026-01-05
  - Duration: 8.0 hours
  - is_overnight: false
```

#### Test 2: Night Shift (Evening → Morning)
```
Date: 2026-01-05
Login: 22:00
Logout: 06:00
Expected Result:
  - Saved to: 2026-01-05
  - Duration: 8.0 hours
  - login_time: 2026-01-05 22:00:00
  - logout_time: 2026-01-06 06:00:00 ← Next day!
  - is_overnight: true
```

#### Test 3: Overwrite Absent with Present
```
Precondition: Employee has "Absent" record for 2026-01-05

Action: Create manual entry
  Date: 2026-01-05
  Login: 20:00
  Logout: 04:00
  Status: present

Expected Result:
  - Old "Absent" record is REPLACED
  - New "Present" record with 8h working time
  - Employee dashboard shows "Present" immediately
```

#### Test 4: Late Night Shift
```
Date: 2026-01-05
Login: 23:30
Logout: 07:45
Expected Result:
  - Duration: 8.25 hours (8h 15m)
  - Correctly spans midnight
```

## Logging

All manual entries are logged with:
```php
\Log::info('Manual Attendance Entry Created/Updated', [
    'admin_user' => 'John Admin',
    'employee_id' => 123,
    'shift_date' => '2026-01-05',
    'login' => '2026-01-05 22:00:00',
    'logout' => '2026-01-06 06:00:00',
    'duration_hours' => 8.0,
    'is_overnight_shift' => true,
    'attendance_id' => 456,
    'action' => 'updated', // or 'created'
]);
```

Check logs: `storage/logs/laravel.log`

## Error Handling

All errors are caught and logged:
```php
\Log::error('Manual Attendance Entry Error', [
    'error' => 'Exception message',
    'trace' => 'Full stack trace',
    'request_data' => [/* submitted form data */],
]);
```

## API Endpoint

**Route**: `POST /attendance/mark-manual`  
**Name**: `attendance.mark-manual.post`  
**Middleware**: `auth`, `role:Super Admin|Manager|...`

**Usage in JavaScript**:
```javascript
fetch('/attendance/mark-manual', {
    method: 'POST',
    body: formData,
    headers: {
        'X-CSRF-TOKEN': csrfToken
    }
})
```

## Migration Notes

### What Was Removed
- ❌ `force_office` parameter (not needed for manual admin entries)
- ❌ Basic date string concatenation
- ❌ Hardcoded field names

### What Was Added
- ✅ Automatic field discovery using `$model->getFillable()`
- ✅ Night shift detection: `if (logout < login) logout->addDay()`
- ✅ Comprehensive logging with overnight indicators
- ✅ Response includes duration and overnight status
- ✅ Real-time UI feedback for overnight shifts

### What Stayed The Same
- ✅ Same form fields (user_id, date, login_time, logout_time, status)
- ✅ Same validation rules
- ✅ Same route name: `attendance.mark-manual.post`
- ✅ Same database table structure
- ✅ Automatic working_hours calculation in model boot

## Troubleshooting

### Issue: Overnight shift shows negative hours
**Cause**: Old logic not adding day to logout  
**Fix**: ✅ Already implemented in new system

### Issue: Manual entry doesn't replace "Absent" record
**Cause**: Using `create()` instead of `updateOrCreate()`  
**Fix**: ✅ Already implemented - uses `updateOrCreate()`

### Issue: Dashboard doesn't update
**Cause**: Browser cache or page not reloading  
**Fix**: ✅ Added `location.reload()` after success

### Issue: Working hours not calculated
**Cause**: Model boot not triggered or times not parseable  
**Fix**: ✅ Added `$attendance->refresh()` and error handling

## Summary

The new system:
1. ✅ Automatically discovers your existing table columns
2. ✅ Correctly handles night shifts (22:00 → 06:00 = 8 hours)
3. ✅ Saves overnight shifts to start date
4. ✅ Overwrites existing records (instant dashboard sync)
5. ✅ Auto-calculates working hours
6. ✅ Provides real-time UI feedback
7. ✅ Logs all operations comprehensively
8. ✅ Handles errors gracefully

**No database changes required** - works with your existing schema!
