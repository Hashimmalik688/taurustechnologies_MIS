# Your CSV File Import Instructions

## ✅ GOOD NEWS: Your File is Ready to Import!

**Case Sensitivity:** ❌ NOT case-sensitive - Your headers will work exactly as they are!

---

## Your Current Columns (✅ All Working Now!)

| # | Your Column Header | Status | System Will Map To |
|---|-------------------|--------|-------------------|
| 1 | Phone Number | ✅ Perfect | phone_number |
| 2 | Customer Name | ✅ Perfect | cn_name |
| 3 | DOB | ✅ Perfect | date_of_birth |
| 4 | HEIGHT & WEIGHT | ✅ Perfect | height_weight |
| 5 | BIRTH PLACE | ✅ Perfect | birth_place |
| 6 | MEDICAL ISSUE | ✅ Perfect | medical_issue |
| 7 | MEDICATIONS | ✅ Perfect | medications |
| 8 | DOC NAME | ✅ Perfect | doctor_name |
| 9 | S.S.N # | ✅ Perfect | ssn |
| 10 | STREET ADRESS | ✅ Perfect (typo OK) | address |
| 11 | CARRIER NAME | ✅ Perfect | carrier_name |
| 12 | COVAERGE AMOUNT | ✅ Perfect (typo OK) | coverage_amount |
| 13 | MONTHLY PREMIUM | ✅ Perfect | monthly_premium |
| 14 | BENEFICIARY | ✅ Perfect | beneficiary |
| 15 | EMERGENCY CONTACT | ✅ Perfect | emergency_contact |
| 16 | INITIAL DRAFT DATE | ✅ Perfect | initial_draft_date |
| 17 | FUTURE DRAFT DATE | ✅ Perfect | future_draft_date |
| 18 | BANK NAME | ✅ Perfect | bank_name |
| 19 | Account title | ✅ **NOW WORKING** | account_title |
| 20 | ACC TYPE | ✅ Perfect | account_type |
| 21 | ROUTING NUMBER | ✅ Perfect | routing_number |
| 22 | ACC NUMBER | ✅ Perfect | acc_number |
| 23 | POLICY TYPE | ✅ Perfect | policy_type |
| 24 | POLICY No. | ✅ **NOW WORKING** | policy_number |
| 25 | ACC VERIFIED BY BANK/CHQ BOOK | ✅ Perfect | account_verified_by |
| 26 | PRESET LINE # | ✅ Perfect | preset_line |
| 27 | Zip Code | ✅ Perfect | zip_code |

---

## 🔧 What I Just Fixed

I added support for your two unique columns:
1. **Account title** → Now imports to `account_title` field
2. **POLICY No.** → Now imports to `policy_number` field

---

## ⚠️ Optional Columns You're Missing (Can Add Later)

These are **NOT required** but will help you track more data:

### Contact Info
- `Secondary Phone` - Alternate phone number
- `State` - State abbreviation (e.g., NY, CA)

### Personal Info
- `Gender` - Male/Female/Other
- `Smoker` - Yes/No
- `Driving License #` - License number

### Beneficiary Details
- `Beneficiary DOB` - Beneficiary date of birth
- *(After import, you can add multiple beneficiaries with relations via the web interface)*

### Payment Info
- `Card Number` - Credit/debit card (encrypted)
- `CVV` - Card security code (encrypted)
- `Expiry Date` - Card expiration

### Tracking
- `Source` - Where lead came from
- `Closer Name` - Sales rep who closed
- `Comments` - Any notes
- `Timestamp` or `Date` - Lead entry date

---

## 📝 Import Instructions

### Step 1: NO REARRANGEMENT NEEDED!
✅ Your columns are in perfect order. **Do NOT change anything!**

### Step 2: Verify Your Data Format

| Field Type | Correct Format | Examples |
|-----------|----------------|----------|
| **Dates** | MM/DD/YYYY | 01/05/2026, 12/31/2025 |
| **Phone** | Any format works | (555) 123-4567, 555-123-4567, 5551234567 |
| **Money** | With/without $ and commas | $250,000 or 250000 or 125.50 |
| **SSN** | Any format | 123-45-6789 or 123456789 |
| **Yes/No** | Yes/No/1/0 | Yes, No, Y, N, 1, 0 |

### Step 3: Save Your File
1. Keep your current headers exactly as they are
2. Save as **CSV (UTF-8)** format
3. Filename: anything.csv (e.g., `leads_jan_2026.csv`)

### Step 4: Import
1. Go to **Leads** → **Import Leads** button
2. Upload your CSV file
3. Wait for import to complete
4. Check imported leads

---

## 🚀 What Will Happen When You Import

✅ **Phone numbers** will be normalized automatically  
✅ **Money values** will be formatted correctly  
✅ **Dates** will be parsed from Excel or MM/DD/YYYY format  
✅ **All 27 columns** will map correctly  
✅ **Typos** in headers (like "ADRESS", "COVAERGE") are handled  
✅ **Case** (UPPER/lower/MiXeD) doesn't matter  

---

## 🔍 After Import - What to Check

1. **Phone Numbers**: Should show in normalized format
2. **Coverage Amount**: Should show as $250,000 (formatted)
3. **Monthly Premium**: Should show as $125.50
4. **Dates**: Should show as MM/DD/YYYY
5. **Policy Number**: Now visible in lead details
6. **Account Title**: Now visible in banking section

---

## ❌ Common Issues & Solutions

### Issue: Some rows don't import
**Check:**
- No completely empty rows
- At least Customer Name OR Phone Number exists
- Dates are valid (not future dates beyond 2050)

### Issue: Money values show as $0.00
**Fix:** Make sure amounts are numbers: `250000` not `Two Hundred Fifty Thousand`

### Issue: Phone numbers missing
**Fix:** Phone column must have actual numbers, not text like "Call for info"

### Issue: Special characters causing errors
**Fix:** 
- Use straight quotes, not curly quotes: `O'Brien` not `O'Brien`
- Avoid: ©, ®, ™, emoji

---

## 📊 Example Row from Your File

```
555-123-4567,John Doe,03/15/1980,5'10" 180lbs,New York,None,None,Dr. Smith,123-45-6789,123 Main St,ABC Insurance,250000,125.50,Jane Doe,555-111-2222,01/15/2026,02/15/2026,Chase Bank,John Doe,Checking,021000021,1234567890,Term Life 20,POL123456,Verified,Partner A,10001
```

This row will import **perfectly** with your current header setup!

---

## ✅ Final Checklist Before Import

- [ ] Headers are exactly as shown above (order doesn't matter)
- [ ] File saved as CSV (UTF-8)
- [ ] Dates in MM/DD/YYYY format
- [ ] Phone numbers have actual digits
- [ ] Money amounts are numbers (not text)
- [ ] No completely empty rows
- [ ] Test with 2-3 rows first before full import

---

## 🎯 Ready to Import!

Your file is **100% compatible** with the system. Just upload it and all 27 columns will import correctly!

**Questions?** Check `/var/www/taurus-crm/storage/logs/laravel.log` if any import fails.
