# Lead Import Guide - Taurus CRM

## Overview
Import leads in bulk using CSV or Excel format. The system automatically normalizes headers and handles missing data intelligently.

## Files Provided
- **Lead_Import_Template.csv** - Sample template with all supported columns and example data

## Column Categories & Acceptable Headers

### PERSONAL INFORMATION
| Column | Acceptable Headers | Example |
|--------|-------------------|---------|
| **Date** | date, timestamp, time stamp | 01/15/2024 |
| **Phone Number** | phone number, phone, contact number, cell phone, cell, mobile, telephone | 2025551234 |
| **Secondary Phone** | secondary phone, second phone, alternate phone, other phone | 2025555678 |
| **Customer Name** | customer name, name | John Smith |
| **Date of Birth** | dob, date of birth | 01/15/1985 |
| **Gender** | gender | Male, Female |
| **Smoker** | smoker | yes, no, true, false |
| **Address** | street address, address, street adress | 123 Main St |
| **State** | state | CA |
| **Zip Code** | zip code, zip, zipcode | 90210 |
| **Birth Place** | birth place, birthplace | Los Angeles |

### IDENTIFICATION
| Column | Acceptable Headers | Example |
|--------|-------------------|---------|
| **SSN** | ssn, ssn #, s.s.n, s.s.n # | 123-45-6789 |
| **Driving License #** | driving license #, driving license number, license number, dl number | D1234567 |
| **Has Driving License** | has driving license | yes, no |

### HEALTH & MEDICAL
| Column | Acceptable Headers | Example |
|--------|-------------------|---------|
| **Height** | height | 5'10", 180cm |
| **Weight** | weight | 180, 180 lbs |
| **Height & Weight** | height & weight, height and weight | 5'10" / 180 |
| **Medical Issue** | medical issue | Diabetes, None |
| **Medications** | medications | Aspirin, Metformin |
| **Doctor Name** | doctor name, doc name | Dr. Johnson |
| **Doctor Number** | doctor number, doctor phone, doc number | 2025551000 |
| **Doctor Address** | doctor address, doc address | 456 Medical Ave |

### INSURANCE & POLICY
| Column | Acceptable Headers | Example |
|--------|-------------------|---------|
| **Carrier Name** | carrier name | Life Insurance Co |
| **Coverage Amount** | coverage amount | 250000, $250,000 |
| **Monthly Premium** | monthly premium, premium | $150, 150, 150.00 |
| **Policy Type** | policy type | Term Life, Whole Life |
| **Policy Number** | policy number, policy no, policy no. | POL123456 |
| **Initial Draft Date** | initial draft date | 01/15/2024 |
| **Future Draft Date** | future draft date | 02/15/2024 |

### BENEFICIARY
| Column | Acceptable Headers | Example |
|--------|-------------------|---------|
| **Beneficiary** | beneficiary | Jane Smith |
| **Beneficiary DOB** | beneficiary dob, beneficiary date of birth | 03/20/1987 |
| **Emergency Contact** | emergency contact | 555-555-1234 |

### BANK ACCOUNT
| Column | Acceptable Headers | Example |
|--------|-------------------|---------|
| **Bank Name** | bank name | First National Bank |
| **Account Title** | account title, acc title | John Smith |
| **Account Type** | account type, acc type | Checking, Savings |
| **Account Number** | account number, acc number | 123456789 |
| **Routing Number** | routing number | 021000021 |
| **Account Verified By** | account verified, account verified by, account verified by bank/chq book | Bank Check |
| **Bank Balance** | bank balance, bank balance /ss amount, date | $5000 |
| **SS Amount** | ss amount, social security amount | $1500 |
| **SS Date** | ss date, social security date | 01/2024 |

### PAYMENT CARD (Sensitive Data)
| Column | Acceptable Headers | Example |
|--------|-------------------|---------|
| **Card Number** | card number, card info | 4111-1111-1111-1111 |
| **CVV** | cvv | 123 |
| **Expiry Date** | expiry date, expiry | 12/2026 |

### ASSIGNMENT & WORKFLOW
| Column | Acceptable Headers | Example |
|--------|-------------------|---------|
| **Source** | source | Facebook, Website, Referral |
| **Team** | team | Sales Team A |
| **Closer Name** | closer name, closer | Agent Name |
| **Assigned Partner** | assigned partner, partner | Partner Name |
| **Preset Line** | preset line, preset | Line A |
| **Comments** | comments, notes | Follow up next week |

## Key Features

### ✓ Flexible Header Matching
- Headers are case-insensitive
- Spaces, hyphens, underscores are ignored
- Use any of the listed variations
- Examples that work:
  - "Phone Number", "phone_number", "PHONE NUMBER", "phone number"
  - "Date of Birth", "DOB", "date_of_birth", "dob"
  - "Customer Name", "customer_name", "CUSTOMER NAME"

### ✓ Automatic Phone Number Processing
- Accepts multiple formats:
  - US: (201) 555-1234, 201-555-1234, 2015551234, +1 201 555 1234
  - Automatically normalizes to 10-digit format
  - Handles leading "1" country code
  - Multiple phone numbers in one field are auto-split (space, comma, slash separated)
  
### ✓ Currency Parsing
- Accepts: $150, 150, $1,234.56, 150k, $3K
- Supports "k" for thousands multiplier
- Converts all to numeric values

### ✓ Date Handling
- Accepts Excel serial dates (numeric)
- Accepts common date formats: 01/15/2024, 2024-01-15, January 15, 2024
- If not provided, uses import date

### ✓ Smoker Field Normalization
- Input: yes, no, true, false, 1, 0
- Stored as: yes or no (enum)

### ✓ Duplicate Detection & Merging
- Matches by phone number (primary or secondary)
- Merges missing fields into existing leads
- Preserves existing data
- Creates new carrier records for duplicates

### ✓ Carrier Auto-Creation
- Automatically creates insurance carrier record
- Extracts: carrier name, coverage amount, premium
- Status set to "pending"

## How to Import

1. **Prepare your CSV/Excel file:**
   - Use the template as reference
   - Only include columns with data (others can be ignored)
   - Use any header variation listed above

2. **Navigate to Lead Import:**
   - Admin Dashboard → Lead Management → Import Leads
   - Or directly access: `/admin/leads/import`

3. **Upload file:**
   - Select CSV or Excel file
   - System processes and shows summary

4. **Review results:**
   - Check import logs for any issues
   - Verify counts: created, updated, duplicates

## Data Privacy & Security

⚠️ **Handle Carefully:**
- SSN, Card Numbers, CVV, DOB are sensitive data
- Access limited to authorized personnel
- Ensure secure file transmission
- Delete import files after processing
- Audit logs track all imports

## Troubleshooting

### Phone Number Issues
- Ensure at least 10 digits
- Remove special characters if not auto-handled
- Check region code (US format expected)

### Date Parsing Errors
- Use standard formats: MM/DD/YYYY or YYYY-MM-DD
- Avoid text-based months if possible
- Excel serial numbers auto-convert

### Duplicate Prevention
- Check existing phone numbers before import
- Use secondary phone field if primary exists
- System logs potential duplicates

### Money Formatting
- Remove $ signs (auto-removed)
- Use . for decimals, not commas in numbers
- Or let system parse: $1,234.56 works fine

## Column Requirements

| Category | Required | Optional |
|----------|----------|----------|
| Phone Number | Highly Recommended | But system accepts leads without phone |
| Customer Name | Recommended | Improves identification |
| All Others | No | All fields optional |

**Notes:**
- Leads can be created without phone numbers
- Phone is primary identifier for duplicates
- SSN secondary identifier (if phone missing)

## Import Limits & Performance

- **File Size:** Up to 5MB per import
- **Rows:** Typically handles 1000+ rows per file
- **Speed:** ~10-50 rows/second depending on data complexity
- **Timeout:** 5 minutes for typical import

## Best Practices

1. ✓ Use consistent date format throughout file
2. ✓ Validate phone numbers before import
3. ✓ Include customer names when possible
4. ✓ Keep carrier name consistent for matching
5. ✓ Test with small batch first (10-20 rows)
6. ✓ Review import logs after each upload
7. ✓ Archive import files for audit trail
8. ✓ Don't import duplicate files multiple times

## Support

For import issues or questions:
- Check `storage/logs/laravel.log` for detailed error messages
- Contact admin for permission issues
- Report data integrity concerns immediately

---

**Last Updated:** March 1, 2026
