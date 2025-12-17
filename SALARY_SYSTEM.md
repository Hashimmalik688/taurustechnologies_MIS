# Automated Salary Calculation System

## Overview
This system automatically calculates employee salaries based on their basic salary, sales performance, attendance, and punctuality.

## Key Features

### 1. **Flexible Employee Configuration**
Each employee can be configured with:
- **Basic Salary**: Monthly base salary amount
- **Sales Employee Flag**: Toggle whether employee has sales targets
- **Sales Target**: Number of sales required to earn basic salary (default: 20)
- **Bonus per Extra Sale**: Amount earned for each sale above target
- **Punctuality Bonus**: Bonus amount if punctuality criteria met

### 2. **Sales-Based Bonus Calculation**
- Employees marked as "Sales Employee" have sales targets
- Basic salary paid if target met (e.g., 20 sales)
- **Bonus Formula**: Extra Sales × Bonus per Sale
  - Example: Target = 20, Actual = 25, Bonus per Sale = Rs500
  - Extra Sales = 25 - 20 = 5
  - Total Bonus = 5 × Rs500 = Rs2,500

- Non-sales employees (HR, Admin, etc.) don't have sales criteria

### 3. **22 Working Days Formula**
- Daily salary = Basic Salary ÷ 22
- Used for calculating leave deductions
- Applied consistently regardless of actual calendar days

### 4. **Leave & Absence Deductions**
- **Full Leave/Off**: Deduct 1 full day salary
- **Half Day**: Deduct 0.5 day salary (half of daily rate)
- **Formula**: Number of Days × Daily Salary

**Example:**
- Basic Salary: Rs44,000
- Daily Salary: Rs44,000 ÷ 22 = Rs2,000
- 2 full leaves + 1 half day = (2 × Rs2,000) + (0.5 × Rs2,000) = Rs5,000 deduction

### 5. **Punctuality Bonus Rules**
Employee **loses** punctuality bonus if any of these occur:
- **1 or more** full offs/leaves
- **2 or more** half days
- **4 or more** late arrivals (after 7:15 AM)

**Late Threshold**: 7:15 AM (any login after 7:15 counts as late)

**Example:**
- Employee has punctuality bonus: Rs3,000
- Attendance: 0 offs, 1 half day, 2 late arrivals
- **Result**: Earns Rs3,000 (qualifies)

- Employee has punctuality bonus: Rs3,000
- Attendance: 1 off, 0 half days, 0 late arrivals
- **Result**: Rs0 (disqualified by 1 off)

### 6. **Automatic Data Collection**
The system automatically:
- Counts sales from Lead database (status = 'accepted', matches employee)
- Retrieves attendance records from Attendance table
- Calculates late arrivals based on 7:15 AM threshold
- Counts half days and full leaves

## Salary Calculation Formula

```
Gross Salary = Basic Salary + Sales Bonus + Punctuality Bonus
Net Salary = Gross Salary - Attendance Deductions

Where:
- Sales Bonus = (Actual Sales - Target Sales) × Bonus per Extra Sale (if positive)
- Punctuality Bonus = Configured amount (if criteria met)
- Attendance Deductions = (Full Leaves × Daily Salary) + (Half Days × Daily Salary × 0.5)
```

## Usage

### Setting Up Employee Salary Settings

1. Navigate to **Salary → Employee Settings**
2. Click **Edit** on any employee
3. Configure:
   - Basic Salary (required)
   - Sales Employee toggle (on for agents, off for admin/HR)
   - Target Sales (if sales employee)
   - Bonus per Extra Sale (if sales employee)
   - Punctuality Bonus (optional)
4. Click **Update Settings**

### Calculating Salaries

**For Specific Employee:**
1. Go to **Salary → Calculate Salaries**
2. Select employee, month, and year
3. Click **Calculate**

**For All Employees:**
1. Go to **Salary → Calculate Salaries**
2. Select month and year
3. Check "All Employees"
4. Click **Calculate All**

### Viewing Salary Records

1. Navigate to **Salary → Records**
2. Filter by employee, month, year, or status
3. Click on any record to see detailed breakdown

### Approving & Paying Salaries

1. View salary record details
2. Review calculations (sales, attendance, deductions)
3. Click **Approve** to mark as approved
4. Click **Mark as Paid** after payment processed

## Data Sources

### Sales Data
- **Source**: `leads` table
- **Criteria**: 
  - `status = 'accepted'`
  - `managed_by` or `closer_name` matches employee
  - `sale_date` within target month

### Attendance Data
- **Source**: `attendances` table
- **Tracks**:
  - Present days (`status = 'present'`)
  - Late arrivals (`login_time > 07:15`)
  - Half days (`status = 'half_day'`)
  - Leaves/Offs (`status = 'leave' or 'absent'`)

## Example Scenarios

### Scenario 1: Sales Employee with Good Performance
```
Employee: John (Sales Agent)
Basic Salary: Rs50,000
Target Sales: 20
Bonus per Sale: Rs750
Punctuality Bonus: Rs3,000

Performance:
- Actual Sales: 28 (8 extra)
- Attendance: 22 present, 0 late, 0 leaves, 0 half days

Calculation:
- Basic Salary: Rs50,000
- Sales Bonus: 8 × Rs750 = Rs6,000
- Punctuality Bonus: Rs3,000 (qualified)
- Deductions: Rs0

Net Salary: Rs59,000
```

### Scenario 2: Sales Employee with Leaves
```
Employee: Sarah (Sales Agent)
Basic Salary: Rs45,000
Target Sales: 20
Bonus per Sale: Rs600
Punctuality Bonus: Rs2,500

Performance:
- Actual Sales: 22 (2 extra)
- Attendance: 19 present, 5 late, 1 leave, 1 half day

Calculation:
- Basic Salary: Rs45,000
- Daily Salary: Rs45,000 ÷ 22 = Rs2,045.45
- Sales Bonus: 2 × Rs600 = Rs1,200
- Punctuality Bonus: Rs0 (5 late = disqualified)
- Deductions: (1 × Rs2,045.45) + (0.5 × Rs2,045.45) = Rs3,068.18

Net Salary: Rs43,131.82
```

### Scenario 3: Non-Sales Employee (HR)
```
Employee: Mike (HR Manager)
Basic Salary: Rs40,000
Is Sales Employee: No
Punctuality Bonus: Rs2,000

Performance:
- Sales: N/A (not tracked)
- Attendance: 22 present, 0 late, 0 leaves, 0 half days

Calculation:
- Basic Salary: Rs40,000
- Sales Bonus: N/A (not sales employee)
- Punctuality Bonus: Rs2,000 (qualified)
- Deductions: Rs0

Net Salary: Rs42,000
```

## Database Tables

### users (Employee Settings)
- `basic_salary` - Monthly base salary
- `target_sales` - Required sales for basic salary
- `bonus_per_extra_sale` - Bonus amount per extra sale
- `punctuality_bonus` - Bonus if punctuality criteria met
- `is_sales_employee` - Flag for sales vs non-sales

### salary_records (Calculated Salaries)
- `basic_salary` - Base amount
- `actual_sales` - Total sales made
- `extra_sales` - Sales above target
- `total_bonus` - Sales + punctuality bonuses
- `working_days` - Always 22
- `present_days`, `leave_days`, `late_days`
- `attendance_deduction` - Total deductions
- `gross_salary`, `net_salary`

### attendances (Daily Attendance)
- `status` - present, late, half_day, leave, absent
- `login_time` - Time of arrival
- `logout_time` - Time of departure

## Tips

1. **Set realistic targets**: Base sales targets on historical performance
2. **Configure punctuality bonus**: Use to incentivize good attendance
3. **Non-sales employees**: Uncheck "Sales Employee" for admin/support staff
4. **Review before approving**: Always check calculation details before approval
5. **Track trends**: Use salary records to identify attendance issues

## API/Routes

- `GET /salary/employees` - Employee settings page
- `PUT /salary/employees/{user}` - Update employee settings
- `POST /salary/calculate` - Calculate salary for specific employees
- `GET /salary/records` - View calculated salary records
- `GET /salary/records/{salaryRecord}` - View detailed salary breakdown

## Support

For issues or questions about salary calculations:
1. Check attendance records are complete
2. Verify employee settings (basic salary, targets)
3. Review sales data in leads table
4. Check calculation logs in salary_records table
