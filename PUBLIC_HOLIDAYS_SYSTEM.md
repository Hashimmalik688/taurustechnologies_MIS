# Public Holidays & Dynamic Attendance Configuration

## Summary of Changes (January 1, 2026)

### Issues Fixed
1. **Hardcoded Time Restrictions**: Replaced hardcoded 6 PM - 6 AM attendance window with dynamic settings-based configuration
2. **Settings Button**: The settings button now properly controls attendance time windows
3. **Public Holidays**: Added complete system for marking public holidays (New Year, Eid, etc.) to skip attendance

---

## What Was Changed

### 1. Database Migration
**File**: `database/migrations/2026_01_01_222503_create_public_holidays_table.php`
- Created `public_holidays` table with fields:
  - `date` (unique) - Holiday date
  - `name` - Holiday name (e.g., "New Year's Day")
  - `description` - Optional notes
  - `is_active` - Toggle to enable/disable holiday

### 2. Public Holiday Model
**File**: `app/Models/PublicHoliday.php`
- Static methods to check if date is a holiday
- Get upcoming holidays
- Get holidays for specific month (for calendar views)

### 3. Updated Attendance Service
**File**: `app/Services/AttendanceService.php`

**Before** (Hardcoded):
```php
$isWithinOfficeHours = $currentHour >= 18 || $currentHour < 6;
```

**After** (Dynamic from Settings):
```php
$bufferHours = (int) Setting::get('attendance_buffer_hours', '1');
$shiftDurationHours = (int) Setting::get('shift_duration_hours', '10');
$windowStart = $startTime->copy()->subHours($bufferHours);
$windowEnd = $startTime->copy()->addHours($shiftDurationHours + $bufferHours);
$isWithinOfficeHours = $currentTime->between($windowStart, $windowEnd, true);
```

**Holiday Check Added**:
```php
if (PublicHoliday::isHoliday($shiftDate)) {
    return [
        'success' => false,
        'message' => 'Today is a public holiday. Attendance marking is not required.',
    ];
}
```

### 4. New Settings Added
**File**: `database/seeders/SettingsSeeder.php`
- `shift_duration_hours` (default: 10) - How many hours is a shift
- `attendance_buffer_hours` (default: 1) - Buffer before/after shift

**Existing Settings Now Used**:
- `office_start_time` (e.g., "19:00" for 7 PM) - When shift starts
- `late_time` (e.g., "19:15") - When to mark as late

### 5. Public Holiday Management Interface
**Routes**: `/admin/public-holidays/*`
**Controller**: `app/Http/Controllers/Admin/PublicHolidayController.php`
**Views**: `resources/views/admin/public-holidays/`
- `index.blade.php` - List all holidays with upcoming section
- `create.blade.php` - Add new holiday (includes common holidays list)
- `edit.blade.php` - Edit/delete holiday

**Added to Sidebar**: New "Public Holidays" menu item (Super Admin only)

---

## How It Works Now

### Attendance Time Window Calculation
1. Gets `office_start_time` from settings (e.g., 19:00 = 7 PM)
2. Gets `shift_duration_hours` (e.g., 10 hours)
3. Gets `attendance_buffer_hours` (e.g., 1 hour)
4. Calculates window: 
   - Start: office_start_time - buffer (18:00 = 6 PM)
   - End: office_start_time + shift_duration + buffer (06:00 = 6 AM next day)

**Example with Default Settings**:
- Office Start: 7:00 PM (19:00)
- Shift Duration: 10 hours
- Buffer: 1 hour
- **Allowed Window**: 6:00 PM to 6:00 AM (matches your current setup!)

### Public Holiday System
1. Super Admin adds holidays via `/admin/public-holidays/create`
2. Specify date, name, description
3. Toggle active/inactive status
4. On holiday dates:
   - Employees cannot mark attendance
   - System shows: "Today is a public holiday. Attendance marking is not required."
   - No absent marking for that day

---

## Usage Instructions

### For Super Admin

#### Configure Attendance Times
1. Go to **Settings** (gear icon in sidebar)
2. Modify these values:
   - **Office Start Time**: When shift begins (e.g., 19:00)
   - **Shift Duration Hours**: How long is the shift (e.g., 10)
   - **Attendance Buffer Hours**: Grace period before/after (e.g., 1)
3. Click **Save Settings**
4. Time window updates immediately!

#### Add Public Holidays
1. Click **Public Holidays** in sidebar
2. Click **Add Holiday** button
3. Fill in:
   - Date (e.g., 2026-01-01 for New Year)
   - Name (e.g., "New Year's Day")
   - Description (optional)
   - Active checkbox (checked = holiday enforced)
4. Click **Add Holiday**

**Common Holidays Provided**:
- New Year's Day (Jan 1)
- Kashmir Day (Feb 5)
- Pakistan Day (Mar 23)
- Labour Day (May 1)
- Independence Day (Aug 14)
- Quaid-e-Azam Day (Dec 25)
- Eid ul-Fitr (you specify date)
- Eid ul-Adha (you specify date)

#### Manage Existing Holidays
- **View**: List shows all holidays with upcoming section at top
- **Edit**: Click pencil icon to modify date/name/description
- **Toggle**: Click Active/Inactive badge to enable/disable
- **Delete**: Click trash icon or use Delete button in edit form

---

## Technical Details

### API Endpoints
- `POST /admin/public-holidays/check-date` - Check if date is holiday (JSON)
- `GET /admin/public-holidays/month?year=2026&month=1` - Get holidays for calendar

### Database Structure
```sql
CREATE TABLE public_holidays (
    id BIGINT PRIMARY KEY,
    date DATE UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Settings Keys
- `office_start_time`: "19:00" or "07:00 PM"
- `shift_duration_hours`: "10"
- `attendance_buffer_hours`: "1"
- `late_time`: "19:15" or "07:15 PM"

---

## Benefits

1. **No More Hardcoding**: Change attendance times via Settings UI
2. **Flexible**: Different shift times for different months/seasons
3. **Holiday Management**: No manual attendance adjustments needed
4. **Transparent**: Employees see clear messages about holidays
5. **Future-Proof**: Add holidays months in advance

---

## Testing Checklist

- [x] Migration ran successfully
- [x] Settings seeder added new fields
- [x] AttendanceService uses dynamic settings
- [x] Public holiday routes accessible
- [x] Views render correctly
- [x] Sidebar link added
- [x] No PHP/Blade errors

---

## Example Scenarios

### Scenario 1: Change Office Hours
**Problem**: Office moves to 8 PM start instead of 7 PM
**Solution**: 
1. Go to Settings
2. Change "Office Start Time" to 20:00
3. Attendance window auto-updates to 7 PM - 7 AM

### Scenario 2: Add Eid Holiday
**Problem**: Eid ul-Fitr on April 10, 2026
**Solution**:
1. Go to Public Holidays
2. Add holiday: Date=2026-04-10, Name="Eid ul-Fitr"
3. On April 10, no one can mark attendance

### Scenario 3: Cancel Holiday
**Problem**: Previously scheduled holiday cancelled
**Solution**:
1. Go to Public Holidays
2. Click Active badge to toggle to Inactive
3. Holiday disabled, attendance resumes

---

## Files Modified/Created

### Created
- `database/migrations/2026_01_01_222503_create_public_holidays_table.php`
- `app/Models/PublicHoliday.php`
- `app/Http/Controllers/Admin/PublicHolidayController.php`
- `resources/views/admin/public-holidays/index.blade.php`
- `resources/views/admin/public-holidays/create.blade.php`
- `resources/views/admin/public-holidays/edit.blade.php`

### Modified
- `app/Services/AttendanceService.php` - Dynamic time windows + holiday checks
- `routes/web.php` - Added public holiday routes
- `database/seeders/SettingsSeeder.php` - Added shift duration & buffer settings
- `resources/views/layouts/sidebar.blade.php` - Added Public Holidays menu link

---

## Support

If attendance window seems wrong:
1. Check Settings page - verify office_start_time
2. Check shift_duration_hours (default: 10)
3. Check attendance_buffer_hours (default: 1)

If holiday not working:
1. Check holiday is Active (green badge)
2. Verify date matches exactly
3. Check timezone (system uses Asia/Karachi)

---

**Deployed**: January 1, 2026  
**Status**: ✅ Complete & Tested
