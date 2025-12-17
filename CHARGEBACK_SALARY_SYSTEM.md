# Chargeback-Adjusted Salary & Revenue System

## 🎯 What Was Implemented

### 1. Chargeback-Adjusted Salary Calculation

#### Logic:
- **Net Approved Sales** = Total Sales - Chargebacks
- Salary bonus based on **net approved** sales, not total

#### Rules:

**Scenario A: Net Below Target**
```
Closer makes: 22 sales
Target: 20 sales
Chargebacks: 4
Net Approved: 22 - 4 = 18 sales

Result:
✓ Basic salary: Paid in full
✗ Sales bonus: Rs0 (didn't meet target)
⚠️ Next month target: 20 + 2 = 22 sales
   (Must make up the 2-sale deficit)
```

**Scenario B: Net Meets Target (No Bonus)**
```
Closer makes: 25 sales
Target: 20 sales
Chargebacks: 5
Net Approved: 25 - 5 = 20 sales

Result:
✓ Basic salary: Paid in full
✗ Sales bonus: Rs0 (exactly at target, no extras)
✓ Next month target: 20 (normal, no adjustment)
```

**Scenario C: Net Above Target (Bonus Earned)**
```
Closer makes: 30 sales
Target: 20 sales
Chargebacks: 5
Net Approved: 30 - 5 = 25 sales

Result:
✓ Basic salary: Paid in full
✓ Sales bonus: 5 extras × Rs750 = Rs3,750
✓ Next month target: 20 (normal, no adjustment)
```

**Scenario D: High Sales, High Chargebacks**
```
Closer makes: 50 sales
Target: 20 sales
Chargebacks: 30
Net Approved: 50 - 30 = 20 sales

Result:
✓ Basic salary: Paid in full (target met)
✗ Sales bonus: Rs0 (no extras after chargebacks)
✓ Next month target: 20 (normal)
✓ Closer not responsible - reached goal despite chargebacks
```

### 2. Revenue Calculation with Chargebacks

#### Formula:
```
Revenue = Premium × 9 months × Commission Rate

Sales Revenue = Sum(All Accepted Sales Revenue)
Chargeback Revenue = Sum(All Chargeback Revenue)
Net Revenue = Sales Revenue - Chargeback Revenue
```

#### Example:
```
Sale 1: Premium Rs5,000 × 9 × 10% = Rs4,500 revenue
Sale 2: Premium Rs8,000 × 9 × 12% = Rs8,640 revenue
Total Sales Revenue: Rs13,140

Chargeback 1: Premium Rs5,000 × 9 × 10% = -Rs4,500 revenue
Total Chargeback Revenue: Rs4,500

Net Revenue: Rs13,140 - Rs4,500 = Rs8,640
```

### 3. Multi-Role Support (Fixed)

**Before:**
- Only showed users with "Employee" role
- Error: "No employees found with 'Employee' role"

**After:**
- Shows **all active users** with basic salary configured
- Displays all user roles as badges (Super Admin, Manager, Agent, etc.)
- Filters by `employment_status = 'active'` and `basic_salary > 0`

## 📊 Database Changes

### New Fields in `salary_records`:
```sql
chargeback_count              INT      -- Number of chargebacks this month
net_approved_sales           INT      -- Total sales - chargebacks
next_month_target_adjustment INT      -- Extra sales needed next month
```

### Example Record:
```json
{
  "user_id": 5,
  "salary_month": 12,
  "salary_year": 2025,
  "basic_salary": 50000,
  "target_sales": 20,
  "actual_sales": 28,           // Total sales made
  "chargeback_count": 6,        // Chargebacks
  "net_approved_sales": 22,     // 28 - 6 = 22
  "extra_sales": 2,             // 22 - 20 = 2
  "bonus_per_extra_sale": 750,
  "total_bonus": 1500,          // 2 × 750
  "next_month_target_adjustment": 0,  // Met target, no adjustment
  "notes": "Sales: 28 total, 6 chargebacks, 22 net approved"
}
```

## 🔧 New Services Created

### `RevenueCalculationService`

**Methods:**
- `calculateMonthlyRevenue($year, $month)` - Get revenue for specific month
- `calculateYearToDateRevenue($year)` - YTD revenue breakdown
- `getDashboardSummary()` - Dashboard stats with growth %
- `getTopPerformers($year, $month, $limit)` - Top earners by revenue

**Usage:**
```php
use App\Services\RevenueCalculationService;

$revenueService = new RevenueCalculationService();

// Get current month revenue
$data = $revenueService->calculateMonthlyRevenue(2025, 12);

echo "Sales: {$data['sales']['count']} = Rs{$data['sales']['revenue']}";
echo "Chargebacks: {$data['chargebacks']['count']} = Rs{$data['chargebacks']['revenue']}";
echo "Net Revenue: Rs{$data['net']['revenue']}";

// Get dashboard summary
$summary = $revenueService->getDashboardSummary();
echo "Growth: {$summary['growth']['revenue_growth_percentage']}%";
```

## 📁 Files Modified

1. **SalaryController.php** - Chargeback logic, multi-role support
2. **SalaryRecord.php** - Added chargeback fields to fillable
3. **employees.blade.php** - Display roles, fixed empty message
4. **RevenueCalculationService.php** - NEW revenue calculator
5. **Migration** - Added chargeback tracking columns

## ✅ How It Works Now

### Salary Calculation Flow:
```
1. User clicks "Calculate Salary"
2. System counts total sales from leads table
3. System counts chargebacks from leads table
4. Calculates: Net Approved = Sales - Chargebacks
5. Compares net approved to target:
   - Below target: Basic only, adjust next month
   - At target: Basic only, normal next month
   - Above target: Basic + bonus on extras
6. Saves with chargeback details
7. Shows warning if next month target adjusted
```

### Dashboard Revenue Calculation:
```php
// For Super Admin dashboard
$revenueService = new RevenueCalculationService();
$revenue = $revenueService->getDashboardSummary(2025, 12);

// Display:
Sales Revenue: Rs150,000 (from 50 sales)
Chargeback Revenue: -Rs30,000 (from 10 chargebacks)
Net Revenue: Rs120,000
Growth: +15.5% from last month
```

## 🎯 Examples with Real Numbers

### Example 1: Deficit Carried Forward
```
Month 1:
- Target: 20
- Made: 22 sales
- Chargebacks: 4
- Net: 18 (below target by 2)
- Salary: Rs50,000 basic + Rs0 bonus
- Next month target: 22

Month 2:
- Target: 22 (20 + 2 carryover)
- Made: 25 sales
- Chargebacks: 1
- Net: 24 (meets adjusted target, 2 extra)
- Salary: Rs50,000 + (2 × Rs750) = Rs51,500
- Next month target: 20 (back to normal)
```

### Example 2: High Sales, High Chargebacks
```
Closer A:
- Target: 20
- Made: 50 sales
- Chargebacks: 25
- Net: 25 (5 above target)
- Salary: Rs50,000 + (5 × Rs750) = Rs53,750
- NOT penalized for chargebacks (met goal)

Closer B:
- Target: 20
- Made: 50 sales
- Chargebacks: 35
- Net: 15 (5 below target)
- Salary: Rs50,000 + Rs0 bonus
- Next month target: 25 (must make up 5)
```

## 🚀 Testing

Run salary calculation for a user:
```bash
# Via web UI:
1. Go to Salary → Calculate Salaries
2. Select employee, month 12, year 2025
3. Click Calculate
4. Review the notes field for chargeback details

# Check database:
SELECT 
    user_id,
    actual_sales,
    chargeback_count,
    net_approved_sales,
    next_month_target_adjustment,
    notes
FROM salary_records
WHERE salary_month = 12 AND salary_year = 2025;
```

Check revenue:
```php
// Add to any controller:
$revenue = app(RevenueCalculationService::class)
    ->calculateMonthlyRevenue(2025, 12);

dd($revenue);
```

## 📝 Notes

- Chargebacks counted from `leads` where `status = 'chargeback'` and `chargeback_marked_date` in month
- Next month target adjustment automatically added to notes
- Revenue service ready for dashboard integration
- All roles now visible in salary employee list
