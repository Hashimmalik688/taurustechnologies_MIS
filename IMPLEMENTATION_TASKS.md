# CRM Enhancement Tasks

## 📋 TASK BREAKDOWN

### 🎯 TASK GROUP 1: Sales Section Enhancements
**Priority: HIGH**

#### Task 1.1: Add New Columns to Sales Section
- [ ] Add "Carrier" column
- [ ] Add "Premium Date/Draft Date" column  
- [ ] Add "Policy Type" column
- [ ] Add "Sale Date" column (if not already present)
- [ ] Add "Month Filter" dropdown

#### Task 1.2: Add Filter Feature to Sales Section
- [ ] Create filter UI (dropdown/multi-select)
- [ ] Filter options needed:
  - Carrier
  - Policy Type
  - Status
  - Date Range
  - Closer Name

#### Task 1.3: Add Search Feature to Sales Section
- [ ] Global search across: customer name, phone, SSN, policy number
- [ ] Real-time search or search button?

---

### 🎯 TASK GROUP 2: All Leads Section Enhancements
**Priority: HIGH**

#### Task 2.1: Add Columns to All Leads Section
- [ ] Add "Carrier" column
- [ ] Add "Premium Date/Draft Date" column
- [ ] Remove "Status" column (as requested - it's just database field)

#### Task 2.2: Add Search Feature to All Leads Section
- [ ] Global search functionality
- [ ] Search across: name, phone, email, SSN

#### Task 2.3: Verify All Required Fields Present
**Need to check if these exist in All Leads:**
- [ ] Smoker (Yes/No)
- [ ] Driving License #
- [ ] Phone Number ✓ (likely exists)
- [ ] Customer Name ✓ (likely exists)
- [ ] DOB
- [ ] Height & Weight
- [ ] Birth Place
- [ ] Medical Issue
- [ ] Medications
- [ ] Doc Name
- [ ] S.S.N #
- [ ] Street Address/Address
- [ ] Carrier Name
- [ ] Coverage Amount
- [ ] Monthly Premium
- [ ] Beneficiary
- [ ] Emergency Contact
- [ ] Initial Draft Date
- [ ] Future Draft Date
- [ ] Bank Name
- [ ] ACC Type (Account Type)
- [ ] Routing Number
- [ ] ACC Number
- [ ] Card Info
- [ ] Policy Type
- [ ] Comments (editable text box)
- [ ] Source
- [ ] Closer Name
- [ ] ACC Verified By (Bank/Chq Book)
- [ ] Bank Balance/SS Amount, Date
- [ ] Preset Line #

---

### 🎯 TASK GROUP 3: Retention Section Enhancements
**Priority: HIGH**

#### Task 3.1: Add New Columns to Retention Section
- [ ] Add "Rewrite/Recover" card/badge column
- [ ] Add "Status" column
- [ ] Add "Month Filter" dropdown

#### Task 3.2: Implement Rewrite Logic
**Business Rule:** When a chargeback occurs this month (e.g., 3rd date), it's considered a "rewrite" if the original sale is 1+ months old.

**Questions to clarify:**
1. What date field determines "sale age"? (sale_at, created_at, or initial_draft_date?)
2. Should "Recover" be manually set or automatic based on some criteria?
3. What statuses are needed in the Status column? (e.g., Pending, Contacted, Recovered, Lost?)

#### Task 3.3: Add Calling System for Retention Officer
- [ ] Enable call popup when connection made (same as Ravens Closer)
- [ ] Ensure Retention Officer role has access to calling functionality
- [ ] Test calling popup integration

---

### 🎯 TASK GROUP 4: Partners Section Enhancements
**Priority: MEDIUM**

#### Task 4.1: Add Carrier Management
- [ ] Create "Add Carrier" button/form
- [ ] Carrier fields needed:
  - Carrier Name
  - Commission Percentage
  - Plan Types (different plans)
  - Age Criteria (min/max age)
  - Calculation Formula

#### Task 4.2: Implement Revenue/Chargeback Calculation
**Formula mentioned:** `premium * 9 (months) * commission_percentage`

**Questions to clarify:**
1. Is "9 months" fixed or variable per carrier?
2. Do different plans within same carrier have different formulas?
3. Age criteria - how does this affect calculations? (e.g., higher premium for older ages?)
4. Should chargeback calculation reduce revenue automatically?
5. Where should calculated revenue/chargeback display? (in Partner view, Sales view, or both?)

#### Task 4.3: Carrier-Specific Settings
- [ ] Multiple plans per carrier support
- [ ] Age brackets (e.g., 18-30, 31-50, 51+)
- [ ] Commission rates per plan/age combination

---

### 🎯 TASK GROUP 5: Chargeback Section Enhancements
**Priority: MEDIUM**

#### Task 5.1: Add Month Filter
- [ ] Add month filter dropdown to Chargeback section
- [ ] Filter by chargeback occurrence month

---

### 🎯 TASK GROUP 6: Data Consistency & Field Standardization
**Priority: CRITICAL - DO THIS FIRST**

#### Task 6.1: Database Schema Review
- [ ] Audit all tables (leads, sales, user_details, etc.)
- [ ] Create field mapping document
- [ ] Identify missing fields
- [ ] Identify inconsistent naming

#### Task 6.2: Create Migration for Missing Fields
All fields that need to be in `leads` table:
```
- smoker (boolean/enum)
- driving_license_number (string)
- phone (string) ✓ exists?
- customer_name (string) ✓ exists as 'name'?
- dob (date)
- height (string)
- weight (string)
- birth_place (string)
- medical_issue (text)
- medications (text)
- doctor_name (string)
- ssn (string)
- street_address (string)
- carrier_name (string) - or carrier_id (foreign key)?
- coverage_amount (decimal)
- monthly_premium (decimal)
- beneficiary (string)
- emergency_contact (string)
- initial_draft_date (date)
- future_draft_date (date)
- bank_name (string)
- account_type (enum: checking/savings)
- routing_number (string)
- account_number (string, encrypted?)
- card_info (string, encrypted?)
- policy_type (string/enum)
- comments (text)
- source (string)
- closer_name (string) - or closer_id (foreign key)?
- account_verified_by (string)
- bank_balance (decimal)
- ss_amount (decimal)
- ss_date (date)
- preset_line_number (string)
```

#### Task 6.3: Standardize Field Names Across CRM
**Current inconsistencies to fix:**
- "Premium Date" vs "Draft Date" vs "Initial Draft Date"
- "Customer Name" vs "Name" vs "Lead Name"
- "Closer Name" vs "Assigned Closer"
- "ACC Number" vs "Account Number"

**Proposed Standard Naming:**
- `initial_draft_date` (when first payment scheduled)
- `future_draft_date` (next payment date)
- `customer_name` (full name)
- `closer_id` (foreign key) with `closer_name` as computed/relationship
- `account_number` (full word)

#### Task 6.4: Update All Forms
- [ ] Update Create Lead form
- [ ] Update Edit Lead form
- [ ] Update Sales forms
- [ ] Update Retention forms
- [ ] Ensure all new fields are editable where needed

#### Task 6.5: Update All List Views
- [ ] Update All Leads table columns
- [ ] Update Sales table columns
- [ ] Update Chargeback table columns
- [ ] Update Retention table columns

---

## ❓ QUESTIONS REQUIRING CLARIFICATION

### Retention Section:
1. **Rewrite Date Logic:** Which date field determines if a sale is "1 month old"?
   - `sale_at`?
   - `created_at`?
   - `initial_draft_date`?

2. **Recover vs Rewrite:** What's the difference?
   - Rewrite = auto-determined by age?
   - Recover = manually marked when retention saves the sale?

3. **Retention Status Options:** What statuses do you need?
   - Suggested: Pending, Contacted, Follow-up Scheduled, Recovered, Lost
   - Or different ones?

### Partners/Carriers:
4. **Commission Formula:** Is `premium * 9 * commission%` the only formula or do carriers vary?
   - Fixed 9 months for all?
   - Or configurable per carrier?

5. **Age Criteria Impact:** How do age brackets affect calculations?
   - Different commission rates per age?
   - Different premium multipliers?
   - Age restrictions (min/max)?

6. **Plans per Carrier:** Examples of different plans?
   - Term Life, Whole Life, Universal Life?
   - Each with different commission structure?

### Data Fields:
7. **Sensitive Data Encryption:** Should these be encrypted?
   - SSN
   - Account Number
   - Routing Number
   - Card Info

8. **Comments Field:** You mentioned "editable text box"
   - Should this be a rich text editor?
   - Or plain textarea?
   - Should there be multiple comment types (internal notes, customer notes)?

9. **Carrier:** Dropdown selection or free text?
   - Should we create a `carriers` table?
   - Pre-defined list or allow adding new ones on-the-fly?

---

## 🚀 RECOMMENDED IMPLEMENTATION ORDER

### Phase 1: Foundation (Week 1)
1. ✅ Answer clarification questions above
2. Database schema audit
3. Create migrations for missing fields
4. Standardize field names

### Phase 2: Core Features (Week 2)
1. Add all missing fields to forms
2. Update All Leads section (columns, search, remove status)
3. Update Sales section (columns, filters, search)

### Phase 3: Advanced Features (Week 3)
1. Retention enhancements (rewrite logic, status, calling system)
2. Add month filters to all sections
3. Chargeback section updates

### Phase 4: Partners/Carriers (Week 4)
1. Carrier management system
2. Calculation formula engine
3. Revenue/chargeback tracking

### Phase 5: Testing & Refinement (Week 5)
1. End-to-end testing
2. Data consistency validation
3. Performance optimization

---

## 📝 NOTES
- Estimated total tasks: **60-70 individual tasks**
- Some tasks blocked pending clarification questions
- Database migrations should be reversible
- Consider data migration for existing records
- Ensure all changes are backward compatible during transition
