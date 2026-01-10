# Lead Import CSV Template Guide

## Template File
📁 **Location:** `/storage/app/public/LEADS_IMPORT_TEMPLATE.csv`

Download this file to see the exact column headers and example data.

---

## Required vs Optional Fields

### ✅ CRITICAL FIELDS (Import will work without, but data won't be useful)
- **Customer Name** - Full name of the customer
- **Phone Number** - Primary contact number (supports multiple formats)

### 🔶 IMPORTANT FIELDS
- **Date of Birth** - Required for insurance quotes
- **SSN** - Required for policy processing
- **Address, State, Zip Code** - Full mailing address
- **Carrier Name** - Insurance company name
- **Coverage Amount** - Policy coverage (e.g., 250000)
- **Monthly Premium** - Premium amount (e.g., 125.50)

### 📋 OPTIONAL BUT RECOMMENDED
- Secondary Phone, Gender, Smoker, Height & Weight, Birth Place
- Medical Issue, Medications, Doctor Name
- Beneficiary, Beneficiary DOB, Emergency Contact
- Policy Type, Initial Draft Date, Future Draft Date
- Bank Name, Account Type, Routing Number, Account Number
- Account Verified By, Bank Balance
- Source, Closer Name, Preset Line #, Comments

---

## Column Header Variations (System Auto-Detects These)

### Phone Number
Accepts any of these headers:
- `Phone Number` ✅ (recommended)
- `Phone`, `Contact Number`, `Cell Phone`, `Cell`, `Mobile`, `Tel`, `Telephone`, `Primary Phone`, `Main Phone`

### Secondary Phone
- `Secondary Phone`, `Second Phone`, `Alternate Phone`, `Other Phone`

### Customer Name
- `Customer Name` ✅ (recommended)
- `Name`

### Date of Birth
- `Date of Birth` ✅ (recommended)
- `DOB`

### SSN
- `SSN` ✅ (recommended)
- `S.S.N`, `S.S.N #`, `SSN #`

### Address Fields
- `Street Address` ✅ (recommended)
- `Address`, `Street Adress`, `Street Address/Address`

### Coverage Amount
- `Coverage Amount` ✅ (recommended)
- `Covaerge Amount` (typo tolerance)

### Monthly Premium
- `Monthly Premium` ✅ (recommended)
- `Premium`

### Beneficiary
- `Beneficiary` ✅ (recommended)
- `Beneficiary DOB`, `Beneficiary Date of Birth`

### Bank Account
- `Account Number` ✅ (recommended)
- `Acc Number`
- `Account Type` / `Acc Type`

### Account Verification
- `Account Verified By` ✅ (recommended)
- `Acc Verified By Bank/Chq Book`, `Account Verified`

### Bank Balance
- `Bank Balance` ✅ (recommended)
- `Bank Balance /SS Amount, Date`, `Bank_Balance`

### Source
- `Source` ✅ (recommended)
- `Source:`

### Closer Name
- `Closer Name` ✅ (recommended)
- `Closer`

---

## Data Format Guidelines

### 📅 Dates
- **Format:** MM/DD/YYYY or Excel date format
- **Examples:** `01/05/2026`, `12/31/2025`

### 💰 Money Values
- Include or exclude `$` symbol (system handles both)
- Use commas or not (system handles both)
- **Examples:** `$250,000`, `250000`, `125.50`

### 📞 Phone Numbers
- Any format works: `(555) 123-4567`, `555-123-4567`, `5551234567`
- System extracts digits and normalizes automatically

### ✅ Yes/No Fields (Smoker, etc.)
- **Yes:** `Yes`, `Y`, `1`
- **No:** `No`, `N`, `0`, or leave empty

### 🔐 SSN
- Any format: `123-45-6789`, `123456789`

---

## Common Import Issues

### ❌ Problem: Phone numbers not importing
**Solution:** Make sure your CSV has one of these column headers:
- Phone Number, Phone, Contact Number, Cell Phone, Mobile, Tel

### ❌ Problem: Data appears in wrong fields
**Solution:** 
1. Check your CSV columns match the template exactly
2. Ensure no extra commas or merged cells
3. Save as CSV (not Excel format) before uploading

### ❌ Problem: Special characters causing errors
**Solution:**
- Avoid special characters in names: Use `John O'Brien` not `John O'Brien`
- Save CSV as UTF-8 encoding

### ❌ Problem: Multiple beneficiaries
**Solution:** 
- Import with one beneficiary in the CSV
- After import, edit the lead to add multiple beneficiaries with relations (Spouse, Child, Parent, etc.)

---

## Step-by-Step Import Process

1. **Download Template** - Use the template CSV as your starting point
2. **Fill Your Data** - Replace the example row with your actual lead data
3. **Verify Headers** - Ensure column headers match exactly (case-insensitive)
4. **Save as CSV** - File → Save As → CSV (UTF-8)
5. **Import** - Go to Leads → Import Leads → Upload your CSV
6. **Review** - Check imported leads for accuracy

---

## Example Good CSV vs Bad CSV

### ✅ GOOD CSV
```
Customer Name,Phone Number,DOB,Coverage Amount
John Doe,555-123-4567,03/15/1980,250000
Jane Smith,(555) 987-6543,06/20/1982,$350,000
```

### ❌ BAD CSV (Problems Highlighted)
```
Name,Cell,Birthday,Policy Amount
John Doe,5551234567,1980-03-15,H536524469440  ← Wrong data type
Jane Smith,,06/20/1982,350000  ← Missing phone
```

---

## Getting Help

If your import fails or data appears incorrect:
1. Check the Laravel logs: `/storage/logs/laravel.log`
2. Verify CSV format matches template exactly
3. Test with just 1-2 rows first before bulk import
4. Contact system administrator with error details

---

**Last Updated:** January 6, 2026  
**System Version:** Taurus CRM v2.0
