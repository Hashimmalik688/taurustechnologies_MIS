# 📊 RAVEN LEADS IMPORT - COMPLETE PACKAGE

## Overview
This package contains everything you need to import leads into the Raven Leads database system.

---

## 📁 INCLUDED FILES

### 1. **RAVEN_LEADS_IMPORT_TEMPLATE.csv** ⭐
- **Purpose:** Ready-to-use CSV template with sample data
- **What to do:** 
  1. Open in Excel or CSV editor
  2. Delete the sample rows (rows 2-3)
  3. Add your lead data
  4. Save as CSV
  5. Import through Raven Leads page

### 2. **RAVEN_LEADS_IMPORT_STRUCTURE.md** 📖
- **Purpose:** Complete technical documentation
- **Contains:**
  - All 45 fields with descriptions
  - Column header variations (50+ accepted names)
  - Deduplication strategy details
  - Data validation rules
  - Mapping to View Lead page
  - Mapping to Ravens calling form
  - Best practices and error handling

### 3. **RAVEN_LEADS_QUICK_START.md** ⚡
- **Purpose:** Quick reference guide
- **Contains:**
  - Minimal required columns
  - Standard recommended columns
  - Complete column list
  - Common mistakes to avoid
  - Quick start template

### 4. **IMPORT_GUIDE.md** 🔧
- **Purpose:** Technical import system documentation
- **Contains:**
  - Import limits and configuration
  - All column name variations
  - Deduplication logic
  - Performance specifications
  - Security measures
  - Troubleshooting guide

---

## 🚀 QUICK START (3 Steps)

### Step 1: Choose Your Approach

**OPTION A - Use Template (Easiest):**
1. Open `RAVEN_LEADS_IMPORT_TEMPLATE.csv`
2. Delete sample rows
3. Add your data
4. Save and import

**OPTION B - Create From Scratch:**
1. Create new Excel/CSV file
2. Copy column headers from QUICK_START guide
3. Add your data
4. Save and import

### Step 2: Minimum Required Data

You MUST have these fields:
- ✅ **Phone Number** (or SSN or Account Number)
- ✅ **Customer Name**
- ✅ **Carrier Name**
- ✅ **Coverage Amount**
- ✅ **Monthly Premium**

### Step 3: Import

1. Go to **Raven Leads** page
2. Click **"Import Leads"** button
3. Select your CSV/Excel file (max 100MB)
4. Click **"Import"**
5. Wait for confirmation

---

## 📋 COMPLETE FIELD LIST (45 Fields)

### Core Fields (Must Have)
```
Phone Number, Customer Name, Carrier Name, Coverage Amount, Monthly Premium
```

### Personal Information (11 fields)
```
Phone Number, Secondary Phone Number, Customer Name, Date of Birth, Gender, 
Age, State, Zip Code, Address, Birth Place, SSN
```

### Medical Information (9 fields)
```
Smoker, Height, Weight, Height & Weight, Driving License #, Medical Issue, 
Medications, Doctor Name, Doctor Number, Doctor Address
```

### Insurance Policy (8 fields)
```
Carrier Name, Coverage Amount, Monthly Premium, Policy Type, Policy Number, 
Initial Draft Date, Future Draft Date, Beneficiary, Beneficiary DOB, 
Emergency Contact
```

### Banking Information (10 fields)
```
Bank Name, Account Title, Account Type, Routing Number, Account Number, 
Account Verified By, Bank Balance, SS Amount, SS Date, Card Number, CVV, 
Expiry Date
```

### Lead Tracking (7 fields)
```
Source, Team, Closer Name, Assigned Partner, Preset Line, Comments
```

---

## 🔍 KEY FEATURES

### ✅ Automatic Deduplication
- Checks Phone Number → SSN → Account Number
- Updates existing leads with missing data
- Creates new leads if no match found

### ✅ Flexible Column Names
- 50+ variations accepted
- Case-insensitive
- Special characters ignored
- Example: "Phone Number" = "phone" = "PHONE_NUMBER"

### ✅ Smart Data Cleaning
- Auto-splits multiple phone numbers
- Removes currency symbols from money fields
- Converts date formats automatically
- Handles empty values gracefully

### ✅ Large File Support
- Up to 100MB per file
- Unlimited rows (memory permitting)
- ~500-1000 rows per minute processing

---

## 📊 FIELD MAPPING

Every import field maps directly to:

### View Lead Page ✓
All 45 fields visible in the lead detail page

### Ravens Calling Form ✓
All fields accessible in Phase 2 and Phase 3 forms

### Database Table ✓
Direct mapping to `leads` table columns

---

## 🎯 COLUMN HEADER VARIATIONS

You don't need exact names! System accepts variations like:

| Your Column | System Understands |
|-------------|-------------------|
| Phone, Cell, Mobile | Phone Number |
| Name, Full Name | Customer Name |
| DOB, Birth Date | Date of Birth |
| SSN, S.S.N # | SSN |
| Premium, Payment | Monthly Premium |
| Carrier, Insurance | Carrier Name |
| Coverage, Face Amount | Coverage Amount |
| Account #, Acct Number | Account Number |
| Routing, ABA | Routing Number |

**Plus 40+ more variations!**

---

## ⚠️ COMMON ISSUES & SOLUTIONS

| Issue | Solution |
|-------|----------|
| File too large (>100MB) | Split into multiple files |
| Import timeout | Reduce rows or contact admin |
| Duplicate data | Don't worry - system handles it |
| Missing required fields | Add Name, Phone, Carrier, Coverage, Premium |
| Wrong date format | Use YYYY-MM-DD |
| No success message | Check storage/logs/laravel.log |

---

## 📞 DATA FROM YOUR SOURCES

The import system accepts data from:

### ✅ View Lead Page
- All fields displayed in lead detail view
- Complete personal, medical, policy, banking info

### ✅ Ravens Calling Form
- Phase 2: Basic info, beneficiaries, carrier details
- Phase 3: Verification form with all fields

### ✅ Peregrine Forms
- Verifier submissions
- Closer updates
- Validator reviews

### ✅ External Sources
- CRM exports
- Lead generation vendors
- Partner data feeds
- Excel spreadsheets

---

## 🎨 FILE FORMAT OPTIONS

### CSV (Recommended)
```csv
Phone Number,Customer Name,Carrier Name,Coverage Amount,Monthly Premium
5551234567,John Smith,American National,100000,150
```

### Excel (XLS/XLSX)
```
Row 1: Headers (Phone Number, Customer Name, etc.)
Row 2+: Your data
```

**Both formats work identically!**

---

## 📈 IMPORT PROCESS

```
1. Prepare File
   ↓
2. Upload (Raven Leads → Import Leads)
   ↓
3. System Processing:
   - Reads headers
   - Normalizes data
   - Checks duplicates
   - Merges or creates leads
   ↓
4. Success Message (Created: X, Updated: Y)
   ↓
5. Verify in Raven Leads table
```

---

## 🔐 SECURITY & VALIDATION

- ✅ CSRF protection
- ✅ File type validation
- ✅ Size limits enforced
- ✅ SQL injection protection
- ✅ Role-based access (CEO, Super Admin, Manager)
- ✅ Transaction rollback on errors

---

## 📚 DOCUMENTATION GUIDE

**Need to:**

| I Want To... | Read This File | Time |
|-------------|---------------|------|
| Get started fast | RAVEN_LEADS_QUICK_START.md | 5 min |
| See sample data | RAVEN_LEADS_IMPORT_TEMPLATE.csv | 2 min |
| Understand all fields | RAVEN_LEADS_IMPORT_STRUCTURE.md | 15 min |
| Deep technical details | IMPORT_GUIDE.md | 30 min |
| Troubleshoot issues | IMPORT_GUIDE.md (Error Handling) | 10 min |

---

## ✨ SUCCESS CHECKLIST

Before importing, verify:

- [ ] File is CSV, XLS, or XLSX format
- [ ] File size is under 100MB
- [ ] First row contains column headers
- [ ] At least these fields: Name, Phone, Carrier, Coverage, Premium
- [ ] Phone numbers are 10 digits (no formatting)
- [ ] Dates are YYYY-MM-DD format
- [ ] Tested with 10-20 rows first
- [ ] Backup created (if updating existing data)

---

## 🎯 RECOMMENDED WORKFLOW

### For New Data Import:
1. Use complete template (45 fields)
2. Fill all available fields
3. Include SSN for better deduplication
4. Test with small batch first
5. Import full file

### For Updates:
1. Export current leads
2. Add/modify data in Excel
3. Re-import (system merges changes)
4. Verify updates in system

### For Large Datasets:
1. Split into 10,000-row chunks
2. Import sequentially
3. Monitor logs between imports
4. Verify counts after each batch

---

## 🏆 BEST PRACTICES

### ✅ Always Do:
- Test with sample data first
- Include SSN for deduplication
- Use consistent date formats
- Fill as many fields as possible
- Check logs after import
- Verify data in system

### ❌ Avoid:
- Mixed date formats in same column
- Files larger than 100MB
- Missing required fields
- Duplicate column names
- Special characters in phone numbers

---

## 🆘 NEED HELP?

### Check These First:
1. **RAVEN_LEADS_QUICK_START.md** - Quick answers
2. **IMPORT_GUIDE.md** - Detailed troubleshooting
3. **storage/logs/laravel.log** - Import logs

### Still Stuck?
- Review the template file for correct format
- Test with just 5 rows
- Check column names match accepted variations
- Verify file size is under 100MB
- Ensure at least minimum required fields present

---

## 🎓 TRAINING RESOURCES

### Video Guides (Coming Soon):
- Basic CSV Import
- Advanced Field Mapping
- Deduplication Explained
- Troubleshooting Common Errors

### Written Guides (Available Now):
- Quick Start Guide
- Complete Structure Documentation
- Import System Technical Guide

---

## 📅 VERSION INFORMATION

- **Last Updated:** February 11, 2026
- **System:** Taurus CRM v2.0
- **Database:** Laravel 11 / MySQL 8
- **Upload Limit:** 100MB
- **Supported Formats:** CSV, XLS, XLSX
- **Fields Supported:** 45 (expandable)

---

## 🚨 IMPORTANT NOTES

1. **Deduplication is Automatic** - System checks Phone, SSN, Account Number
2. **Updates are Non-Destructive** - Existing data is never overwritten
3. **Carriers Always Added** - Multiple carriers per lead supported
4. **Status Auto-Set** - Imported leads get status='closed', source_type='imported'
5. **Logs Everything** - Check storage/logs/laravel.log for details

---

## 💡 PRO TIPS

1. **Use Excel for Data Prep** - Easier than text editors
2. **Include SSN** - Better deduplication accuracy
3. **Test Small First** - 10-20 rows before full import
4. **Save as CSV** - More reliable than XLSX for large files
5. **Check Logs** - Always review after large imports
6. **Keep Backups** - Export before re-importing updates

---

## ✅ YOU'RE READY!

You now have everything needed to import leads:
- ✅ Template file with sample data
- ✅ Complete field documentation
- ✅ Quick start guide
- ✅ Technical reference
- ✅ Troubleshooting guide

**Start with the template, add your data, and import!**

---

**Questions?** Review the documentation files in this package.  
**Issues?** Check storage/logs/laravel.log for details.  
**Ready?** Go to Raven Leads → Import Leads → Upload your file!

---

**Package Contents:**
1. RAVEN_LEADS_IMPORT_TEMPLATE.csv (Sample data file)
2. RAVEN_LEADS_IMPORT_STRUCTURE.md (Complete field documentation)
3. RAVEN_LEADS_QUICK_START.md (Quick reference guide)
4. IMPORT_GUIDE.md (Technical system documentation)
5. README_IMPORT_PACKAGE.md (This file)

**Happy Importing! 🎉**