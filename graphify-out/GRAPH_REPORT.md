# Graph Report - taurus-crm  (2026-04-29)

## Corpus Check
- 1288 files · ~1,922,673 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 9722 nodes · 27248 edges · 106 communities detected
- Extraction: 69% EXTRACTED · 31% INFERRED · 0% AMBIGUOUS · INFERRED: 8534 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- [[_COMMUNITY_Community 0|Community 0]]
- [[_COMMUNITY_Community 1|Community 1]]
- [[_COMMUNITY_Community 2|Community 2]]
- [[_COMMUNITY_Community 3|Community 3]]
- [[_COMMUNITY_Community 4|Community 4]]
- [[_COMMUNITY_Community 5|Community 5]]
- [[_COMMUNITY_Community 6|Community 6]]
- [[_COMMUNITY_Community 7|Community 7]]
- [[_COMMUNITY_Community 8|Community 8]]
- [[_COMMUNITY_Community 9|Community 9]]
- [[_COMMUNITY_Community 10|Community 10]]
- [[_COMMUNITY_Community 11|Community 11]]
- [[_COMMUNITY_Community 12|Community 12]]
- [[_COMMUNITY_Community 13|Community 13]]
- [[_COMMUNITY_Community 14|Community 14]]
- [[_COMMUNITY_Community 15|Community 15]]
- [[_COMMUNITY_Community 16|Community 16]]
- [[_COMMUNITY_Community 17|Community 17]]
- [[_COMMUNITY_Community 18|Community 18]]
- [[_COMMUNITY_Community 19|Community 19]]
- [[_COMMUNITY_Community 20|Community 20]]
- [[_COMMUNITY_Community 21|Community 21]]
- [[_COMMUNITY_Community 22|Community 22]]
- [[_COMMUNITY_Community 23|Community 23]]
- [[_COMMUNITY_Community 24|Community 24]]
- [[_COMMUNITY_Community 25|Community 25]]
- [[_COMMUNITY_Community 26|Community 26]]
- [[_COMMUNITY_Community 27|Community 27]]
- [[_COMMUNITY_Community 28|Community 28]]
- [[_COMMUNITY_Community 29|Community 29]]
- [[_COMMUNITY_Community 30|Community 30]]
- [[_COMMUNITY_Community 33|Community 33]]
- [[_COMMUNITY_Community 34|Community 34]]
- [[_COMMUNITY_Community 35|Community 35]]
- [[_COMMUNITY_Community 36|Community 36]]
- [[_COMMUNITY_Community 38|Community 38]]
- [[_COMMUNITY_Community 39|Community 39]]
- [[_COMMUNITY_Community 40|Community 40]]
- [[_COMMUNITY_Community 41|Community 41]]
- [[_COMMUNITY_Community 42|Community 42]]
- [[_COMMUNITY_Community 43|Community 43]]
- [[_COMMUNITY_Community 44|Community 44]]
- [[_COMMUNITY_Community 45|Community 45]]
- [[_COMMUNITY_Community 46|Community 46]]
- [[_COMMUNITY_Community 47|Community 47]]
- [[_COMMUNITY_Community 48|Community 48]]
- [[_COMMUNITY_Community 49|Community 49]]
- [[_COMMUNITY_Community 50|Community 50]]
- [[_COMMUNITY_Community 51|Community 51]]
- [[_COMMUNITY_Community 52|Community 52]]
- [[_COMMUNITY_Community 53|Community 53]]
- [[_COMMUNITY_Community 54|Community 54]]
- [[_COMMUNITY_Community 55|Community 55]]
- [[_COMMUNITY_Community 56|Community 56]]
- [[_COMMUNITY_Community 57|Community 57]]
- [[_COMMUNITY_Community 58|Community 58]]
- [[_COMMUNITY_Community 59|Community 59]]
- [[_COMMUNITY_Community 60|Community 60]]
- [[_COMMUNITY_Community 61|Community 61]]
- [[_COMMUNITY_Community 62|Community 62]]
- [[_COMMUNITY_Community 63|Community 63]]
- [[_COMMUNITY_Community 64|Community 64]]
- [[_COMMUNITY_Community 65|Community 65]]
- [[_COMMUNITY_Community 66|Community 66]]
- [[_COMMUNITY_Community 67|Community 67]]
- [[_COMMUNITY_Community 68|Community 68]]
- [[_COMMUNITY_Community 69|Community 69]]
- [[_COMMUNITY_Community 70|Community 70]]
- [[_COMMUNITY_Community 71|Community 71]]
- [[_COMMUNITY_Community 72|Community 72]]
- [[_COMMUNITY_Community 73|Community 73]]
- [[_COMMUNITY_Community 74|Community 74]]
- [[_COMMUNITY_Community 75|Community 75]]
- [[_COMMUNITY_Community 76|Community 76]]
- [[_COMMUNITY_Community 77|Community 77]]
- [[_COMMUNITY_Community 78|Community 78]]
- [[_COMMUNITY_Community 79|Community 79]]
- [[_COMMUNITY_Community 80|Community 80]]
- [[_COMMUNITY_Community 81|Community 81]]
- [[_COMMUNITY_Community 82|Community 82]]
- [[_COMMUNITY_Community 83|Community 83]]
- [[_COMMUNITY_Community 84|Community 84]]
- [[_COMMUNITY_Community 85|Community 85]]
- [[_COMMUNITY_Community 86|Community 86]]
- [[_COMMUNITY_Community 87|Community 87]]
- [[_COMMUNITY_Community 88|Community 88]]
- [[_COMMUNITY_Community 89|Community 89]]
- [[_COMMUNITY_Community 90|Community 90]]
- [[_COMMUNITY_Community 91|Community 91]]
- [[_COMMUNITY_Community 95|Community 95]]
- [[_COMMUNITY_Community 105|Community 105]]
- [[_COMMUNITY_Community 112|Community 112]]
- [[_COMMUNITY_Community 128|Community 128]]
- [[_COMMUNITY_Community 154|Community 154]]
- [[_COMMUNITY_Community 185|Community 185]]
- [[_COMMUNITY_Community 186|Community 186]]
- [[_COMMUNITY_Community 187|Community 187]]
- [[_COMMUNITY_Community 188|Community 188]]
- [[_COMMUNITY_Community 189|Community 189]]
- [[_COMMUNITY_Community 190|Community 190]]
- [[_COMMUNITY_Community 191|Community 191]]
- [[_COMMUNITY_Community 192|Community 192]]
- [[_COMMUNITY_Community 193|Community 193]]
- [[_COMMUNITY_Community 194|Community 194]]
- [[_COMMUNITY_Community 219|Community 219]]
- [[_COMMUNITY_Community 220|Community 220]]

## God Nodes (most connected - your core abstractions)
1. `push()` - 593 edges
2. `min()` - 327 edges
3. `max()` - 259 edges
4. `get()` - 256 edges
5. `count()` - 242 edges
6. `map()` - 211 edges
7. `each()` - 195 edges
8. `View()` - 186 edges
9. `slice()` - 184 edges
10. `round()` - 180 edges

## Surprising Connections (you probably didn't know these)
- `isEditable()` --calls--> `getAttribute()`  [INFERRED]
  public/js/crm-security.js → resources/libs/echarts/echarts.esm.mjs
- `db()` --calls--> `up()`  [INFERRED]
  resources/libs/echarts/echarts.esm.min.mjs → database/migrations/2026_04_27_120000_add_ledger_sales_return_status_to_leads_table.php
- `db()` --calls--> `down()`  [INFERRED]
  resources/libs/echarts/echarts.esm.min.mjs → database/migrations/2026_04_06_210001_add_sales_return_type_and_lead_return_link.php
- `Module` --calls--> `down()`  [INFERRED]
  app/Models/Module.php → database/migrations/2026_02_23_000002_remove_dead_holidays_notifications_modules.php
- `Module` --calls--> `up()`  [INFERRED]
  app/Models/Module.php → database/migrations/2026_04_10_000001_add_qa_scoring_module.php

## Communities

### Community 0 - "Community 0"
Cohesion: 0.0
Nodes (1583): l(), initData(), add(), addAnimation(), addChild(), addCommas(), addEl(), addEventListener() (+1575 more)

### Community 1 - "Community 1"
Cohesion: 0.0
Nodes (290): down(), down(), up(), down(), up(), down(), up(), down() (+282 more)

### Community 2 - "Community 2"
Cohesion: 0.01
Nodes (441): getChartColorsArray(), abstract(), addBox(), addIfFound(), addIfString(), addListener(), addNormalRectPath(), addPointsBelow() (+433 more)

### Community 3 - "Community 3"
Cohesion: 0.01
Nodes (742): ka(), _addToZip(), _excelColWidth(), _addToZip(), _excelColWidth(), afterDatasetsUpdate(), allPlugins(), getDistanceMetricForAxis() (+734 more)

### Community 4 - "Community 4"
Cohesion: 0.01
Nodes (286): i(), Authenticate, clipBounds(), clipVertical(), doFill(), drawPointLabelBox(), fill(), getTooltipSize() (+278 more)

### Community 5 - "Community 5"
Cohesion: 0.01
Nodes (378): alert(), t(), getLanguage(), init(), initActiveMenu(), initCheckAll(), initComponents(), initDropdownMenu() (+370 more)

### Community 6 - "Community 6"
Cohesion: 0.01
Nodes (119): CreateUsersTable, down(), getConnection(), up(), CreateFailedJobsTable, up(), up(), up() (+111 more)

### Community 7 - "Community 7"
Cohesion: 0.01
Nodes (246): f(), setEventListener(), removeListener(), doEnter(), Handler(), onLeave(), updateBBoxFromPoints(), wrapped() (+238 more)

### Community 8 - "Community 8"
Cohesion: 0.01
Nodes (195): d(), h(), u(), z(), _createNode(), createCellPos(), _createNode(), n() (+187 more)

### Community 9 - "Community 9"
Cohesion: 0.02
Nodes (224): createNewSchedule(), CallStatusChanged, getTime(), week(), dateGenerator(), floorInBase(), formatDate(), init() (+216 more)

### Community 10 - "Community 10"
Cohesion: 0.02
Nodes (235): a(), aa(), Ae(), ai(), an(), ao(), Ar(), at() (+227 more)

### Community 11 - "Community 11"
Cohesion: 0.04
Nodes (4): partials.custom-select-datepicker-styles, partials.pipeline-dashboard-styles, partials.sl-filter-assets, peregrine.closers.form

### Community 12 - "Community 12"
Cohesion: 0.04
Nodes (7): Notification, NotificationService, PermissionLevel, getOriginalPhoneNumber(), sanitizePhoneForChannel(), SidebarHelper, User

### Community 13 - "Community 13"
Cohesion: 0.04
Nodes (1): Lead

### Community 14 - "Community 14"
Cohesion: 0.11
Nodes (2): SalaryComponent, SalaryService

### Community 15 - "Community 15"
Cohesion: 0.13
Nodes (7): CleanupAnalyze, confineTooltipPosition(), getComputedStyle(), getSize(), parseInt10(), refixTooltipPosition(), FileUploadService

### Community 16 - "Community 16"
Cohesion: 0.16
Nodes (3): Announcement, AnnouncementController, AnnouncementSeeder

### Community 17 - "Community 17"
Cohesion: 0.11
Nodes (1): admin.accounting._nav

### Community 18 - "Community 18"
Cohesion: 0.15
Nodes (12): addLeadingZero(), _classCallCheck(), _createClass(), Datepicker(), _defineProperties(), getDaysInMonth(), getMinDay(), getScrollParent() (+4 more)

### Community 19 - "Community 19"
Cohesion: 0.12
Nodes (1): ZoomWebhookLog

### Community 20 - "Community 20"
Cohesion: 0.14
Nodes (1): SalaryRecord

### Community 21 - "Community 21"
Cohesion: 0.14
Nodes (1): EPMSTask

### Community 22 - "Community 22"
Cohesion: 0.36
Nodes (1): AnnouncementPolicy

### Community 23 - "Community 23"
Cohesion: 0.2
Nodes (1): LedgerJournalEntry

### Community 24 - "Community 24"
Cohesion: 0.22
Nodes (2): AccountSwitchingDetector, LoginController

### Community 25 - "Community 25"
Cohesion: 0.22
Nodes (1): CallLog

### Community 26 - "Community 26"
Cohesion: 0.25
Nodes (1): Community

### Community 27 - "Community 27"
Cohesion: 0.32
Nodes (1): InsuranceCarrier

### Community 28 - "Community 28"
Cohesion: 0.25
Nodes (1): QaResult

### Community 29 - "Community 29"
Cohesion: 0.29
Nodes (6): components.freeloaders-widget, components.sticky-notes, components.zoom-phone-widget, layouts.head-css, layouts.sidebar, layouts.vendor-scripts

### Community 30 - "Community 30"
Cohesion: 0.29
Nodes (1): CreateLead

### Community 33 - "Community 33"
Cohesion: 0.29
Nodes (1): UserDetail

### Community 34 - "Community 34"
Cohesion: 0.29
Nodes (1): components.hub-styles

### Community 35 - "Community 35"
Cohesion: 0.33
Nodes (1): CommunityAnnouncementPosted

### Community 36 - "Community 36"
Cohesion: 0.33
Nodes (1): RoleChangedMail

### Community 38 - "Community 38"
Cohesion: 0.53
Nodes (5): lastNumber(), mutation(), relativeTimeWithMutation(), softMutation(), specialMutationForYears()

### Community 39 - "Community 39"
Cohesion: 0.6
Nodes (4): forms(), special(), translate(), translateSingular()

### Community 40 - "Community 40"
Cohesion: 0.4
Nodes (1): MessageSent

### Community 41 - "Community 41"
Cohesion: 0.4
Nodes (1): AccountCreatedMail

### Community 42 - "Community 42"
Cohesion: 0.5
Nodes (1): TelescopeServiceProvider

### Community 43 - "Community 43"
Cohesion: 0.5
Nodes (1): AppServiceProvider

### Community 44 - "Community 44"
Cohesion: 0.5
Nodes (1): NamespaceFixer

### Community 45 - "Community 45"
Cohesion: 0.4
Nodes (1): MentionedInChatNotification

### Community 46 - "Community 46"
Cohesion: 0.4
Nodes (1): MentionedInAnnouncementNotification

### Community 47 - "Community 47"
Cohesion: 0.4
Nodes (1): Kernel

### Community 48 - "Community 48"
Cohesion: 0.4
Nodes (1): Carrier

### Community 49 - "Community 49"
Cohesion: 0.5
Nodes (1): MarkAttendanceOnLogin

### Community 50 - "Community 50"
Cohesion: 0.5
Nodes (1): CheckDailyAttendance

### Community 51 - "Community 51"
Cohesion: 0.4
Nodes (1): UpdateUserRequest

### Community 52 - "Community 52"
Cohesion: 0.4
Nodes (1): UpdateLeadRequest

### Community 53 - "Community 53"
Cohesion: 0.4
Nodes (1): StoreUserRequest

### Community 54 - "Community 54"
Cohesion: 0.4
Nodes (1): UpdateAgentRequest

### Community 55 - "Community 55"
Cohesion: 0.4
Nodes (1): StoreAgentRequest

### Community 56 - "Community 56"
Cohesion: 0.4
Nodes (1): StoreLedgerEntryRequest

### Community 57 - "Community 57"
Cohesion: 0.4
Nodes (1): StoreLeadRequest

### Community 58 - "Community 58"
Cohesion: 0.6
Nodes (3): eifelerRegelAppliesToNumber(), processFutureTime(), processPastTime()

### Community 59 - "Community 59"
Cohesion: 0.6
Nodes (3): format(), relativeTimeWithPlural(), relativeTimeWithSingular()

### Community 60 - "Community 60"
Cohesion: 0.4
Nodes (1): partner.partials.carrier-filter

### Community 61 - "Community 61"
Cohesion: 0.5
Nodes (1): LeadCreated

### Community 62 - "Community 62"
Cohesion: 0.5
Nodes (1): SaleCreated

### Community 63 - "Community 63"
Cohesion: 0.5
Nodes (1): CarrierAliases

### Community 64 - "Community 64"
Cohesion: 0.5
Nodes (1): CarrierCommissionBracket

### Community 65 - "Community 65"
Cohesion: 0.5
Nodes (1): ChatMessageRead

### Community 66 - "Community 66"
Cohesion: 0.5
Nodes (1): LeadFieldHighlight

### Community 67 - "Community 67"
Cohesion: 0.5
Nodes (1): Partner

### Community 68 - "Community 68"
Cohesion: 0.5
Nodes (1): LocalizationController

### Community 69 - "Community 69"
Cohesion: 0.5
Nodes (1): init()

### Community 70 - "Community 70"
Cohesion: 0.67
Nodes (1): EventServiceProvider

### Community 71 - "Community 71"
Cohesion: 0.67
Nodes (1): BroadcastServiceProvider

### Community 72 - "Community 72"
Cohesion: 0.67
Nodes (1): AuthServiceProvider

### Community 73 - "Community 73"
Cohesion: 0.67
Nodes (1): CheckModulePermissionWithRole

### Community 74 - "Community 74"
Cohesion: 0.67
Nodes (1): StickyNote

### Community 75 - "Community 75"
Cohesion: 0.67
Nodes (1): EPMSExternalCost

### Community 76 - "Community 76"
Cohesion: 0.67
Nodes (1): SendLeadCreatedNotification

### Community 77 - "Community 77"
Cohesion: 0.67
Nodes (1): LogUserLogout

### Community 78 - "Community 78"
Cohesion: 0.67
Nodes (1): Handler

### Community 79 - "Community 79"
Cohesion: 0.67
Nodes (1): TrustHosts

### Community 80 - "Community 80"
Cohesion: 0.67
Nodes (1): CheckModulePermission

### Community 81 - "Community 81"
Cohesion: 0.67
Nodes (1): VerificationController

### Community 82 - "Community 82"
Cohesion: 0.67
Nodes (1): UserObserver

### Community 83 - "Community 83"
Cohesion: 1.0
Nodes (2): plural(), translate()

### Community 84 - "Community 84"
Cohesion: 1.0
Nodes (2): plural(), translate()

### Community 85 - "Community 85"
Cohesion: 1.0
Nodes (2): plural(), translate()

### Community 86 - "Community 86"
Cohesion: 1.0
Nodes (2): plural(), translate()

### Community 87 - "Community 87"
Cohesion: 1.0
Nodes (2): translate(), verbalNumber()

### Community 88 - "Community 88"
Cohesion: 1.0
Nodes (2): plural(), relativeTimeWithPlural()

### Community 89 - "Community 89"
Cohesion: 1.0
Nodes (2): plural(), relativeTimeWithPlural()

### Community 90 - "Community 90"
Cohesion: 0.67
Nodes (1): admin.agents.partials.carrier-states

### Community 91 - "Community 91"
Cohesion: 0.67
Nodes (1): admin.epms.partials.wbs-item

### Community 95 - "Community 95"
Cohesion: 0.67
Nodes (1): up()

### Community 105 - "Community 105"
Cohesion: 0.67
Nodes (1): up()

### Community 112 - "Community 112"
Cohesion: 0.67
Nodes (1): RoleSeeder

### Community 128 - "Community 128"
Cohesion: 0.67
Nodes (1): up()

### Community 154 - "Community 154"
Cohesion: 0.67
Nodes (1): up()

### Community 185 - "Community 185"
Cohesion: 0.67
Nodes (1): DatabaseSeeder

### Community 186 - "Community 186"
Cohesion: 0.67
Nodes (1): InsuranceCarrierSeeder

### Community 187 - "Community 187"
Cohesion: 1.0
Nodes (1): Teams

### Community 188 - "Community 188"
Cohesion: 1.0
Nodes (1): Statuses

### Community 189 - "Community 189"
Cohesion: 1.0
Nodes (1): PettyCashLedger

### Community 190 - "Community 190"
Cohesion: 1.0
Nodes (1): TrimStrings

### Community 191 - "Community 191"
Cohesion: 1.0
Nodes (1): TrustProxies

### Community 192 - "Community 192"
Cohesion: 1.0
Nodes (1): EncryptCookies

### Community 193 - "Community 193"
Cohesion: 1.0
Nodes (1): PreventRequestsDuringMaintenance

### Community 194 - "Community 194"
Cohesion: 1.0
Nodes (1): Controller

### Community 219 - "Community 219"
Cohesion: 1.0
Nodes (1): admin.leads.index_table

### Community 220 - "Community 220"
Cohesion: 1.0
Nodes (1): admin.partners.partials.carrier-states

## Knowledge Gaps
- **17 isolated node(s):** `Teams`, `Statuses`, `PettyCashLedger`, `TrimStrings`, `TrustProxies` (+12 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **Thin community `Community 13`** (51 nodes): `Lead.php`, `Lead`, `.assignedAgent()`, `.assignedCloser()`, `.assignedValidator()`, `.bankVerifiedByUser()`, `.bankVerifier()`, `.bankVerifierAssignedByUser()`, `.boot()`, `.callLogs()`, `.carriers()`, `.cbSentToRetentionBy()`, `.chargebackMarkedBy()`, `.chargebackPaidBy()`, `.dials()`, `.dispositionOfficer()`, `.fieldHighlights()`, `.followupAssignedByUser()`, `.followupDoneBy()`, `.followupPerson()`, `.forwardedBy()`, `.getAgeAttribute()`, `.insuranceCarrier()`, `.issuedByUser()`, `.ledgerChargebackPaidEntry()`, `.ledgerEntries()`, `.ledgerSalesReturnEntry()`, `.ledgerSalesReturnPostedBy()`, `.managedBy()`, `.notIssuedBy()`, `.notIssuedResolvedBy()`, `.notPaidBy()`, `.paidBy()`, `.partner()`, `.pendingContractBy()`, `.pendingDraftBy()`, `.policyDiedBy()`, `.qaUser()`, `.recallRequestedBy()`, `.retActionUpdatedBy()`, `.retentionOfficer()`, `.scopeFollowupPending()`, `.scopeNotIssued()`, `.scopePaidSales()`, `.scopePendingContract()`, `.scopePendingDraft()`, `.scopePendingsApproved()`, `.scopePolicyDied()`, `.submissionReviewer()`, `.validator()`, `.verifier()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 14`** (24 nodes): `SalaryComponent.php`, `SalaryService.php`, `SalaryComponent`, `.deductions()`, `.getAttendancePercentageAttribute()`, `.getComponentLabelAttribute()`, `.getHasPerfectAttendanceAttribute()`, `.getHasSalesDataAttribute()`, `.getMonthNameAttribute()`, `.getSalesTargetStatusAttribute()`, `.scopeBasic()`, `.scopeBonus()`, `.scopeForPeriod()`, `.scopeUnpaid()`, `.user()`, `SalaryService`, `.calculateBasicSalary()`, `.calculateBonusSalary()`, `.__construct()`, `.createSalaryComponents()`, `.getAttendanceData()`, `.getPaymentDate()`, `.getSalesData()`, `.qualifiesForPunctualityBonus()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 17`** (19 nodes): `admin.accounting._nav`, `dashboard.blade.php`, `create.blade.php`, `index.blade.php`, `show.blade.php`, `carrier-show.blade.php`, `index.blade.php`, `overview.blade.php`, `chargeback.blade.php`, `opening-balance.blade.php`, `payment.blade.php`, `sale.blade.php`, `balance-sheet.blade.php`, `expense-tracker.blade.php`, `profit-loss.blade.php`, `trial-balance.blade.php`, `index.blade.php`, `partner.blade.php`, `returns.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 19`** (17 nodes): `ZoomWebhookLog.php`, `ZoomWebhookLog`, `.agent()`, `.getDisplayNameAttribute()`, `.getDisplayPhoneAttribute()`, `.getFormattedDurationAttribute()`, `.isLinkedToMis()`, `.lead()`, `.matchedCallLog()`, `.scopeAnswered()`, `.scopeDateRange()`, `.scopeEventType()`, `.scopeMissed()`, `.scopeUnprocessed()`, `.scopeWithRecording()`, `.scopeWithTranscript()`, `.wasAnswered()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 20`** (16 nodes): `SalaryRecord.php`, `SalaryRecord`, `.basicComponent()`, `.bonusComponent()`, `.components()`, `.deductions()`, `.dockRecords()`, `.getAttendancePercentageAttribute()`, `.getAttendanceSummaryAttribute()`, `.getHasPerfectAttendanceAttribute()`, `.getMonthNameAttribute()`, `.getNetAttendanceImpactAttribute()`, `.getPunctualityPercentageAttribute()`, `.getSandwichPenaltyDaysAttribute()`, `.getTotalSalaryAdjustmentAttribute()`, `.user()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 21`** (14 nodes): `EPMSTask.php`, `EPMSTask`, `.assignedUser()`, `.comments()`, `.dependencies()`, `.dependents()`, `.dependentTasks()`, `.dependsOnTasks()`, `.documents()`, `.getDurationAttribute()`, `.getIsOverdueAttribute()`, `.milestone()`, `.project()`, `.sprint()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 22`** (10 nodes): `AnnouncementPolicy`, `.create()`, `.delete()`, `.forceDelete()`, `.isAuthorized()`, `.restore()`, `.update()`, `.view()`, `.viewAny()`, `AnnouncementPolicy.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 23`** (10 nodes): `LedgerJournalEntry.php`, `LedgerJournalEntry`, `.creator()`, `.generateEntryNumber()`, `.getTypeLabelAttribute()`, `.lead()`, `.lines()`, `.scopeDateRange()`, `.scopeOfType()`, `.typeLabel()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 24`** (9 nodes): `AccountSwitchingDetector`, `.logSuspiciousActivity()`, `LoginController.php`, `AccountSwitchingDetector.php`, `LoginController`, `.authenticated()`, `.__construct()`, `.credentials()`, `.redirectTo()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 25`** (9 nodes): `CallLog.php`, `CallLog`, `.agent()`, `.creator()`, `.getFormattedDurationAttribute()`, `.lead()`, `.scopeByAgent()`, `.scopeNeedsFollowUp()`, `.scopeSuccessful()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 26`** (8 nodes): `Community.php`, `Community`, `.announcements()`, `.chatConversations()`, `.communityAnnouncements()`, `.createdByManagers()`, `.creator()`, `.members()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 27`** (8 nodes): `InsuranceCarrier.php`, `InsuranceCarrier`, `.agentCommissions()`, `.agentStates()`, `.commissionBrackets()`, `.getCommissionForAge()`, `.getCommissionForAgent()`, `.leads()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 28`** (8 nodes): `QaResult.php`, `QaResult`, `.complianceFlags()`, `.getComplianceChecksAttribute()`, `.getScoreBreakdownAttribute()`, `.qaCall()`, `.scopeComplianceFail()`, `.scopeVoidRisk()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 30`** (7 nodes): `CreateLead.php`, `CreateLead`, `.addBeneficiary()`, `.mount()`, `.removeBeneficiary()`, `.render()`, `.updated()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 33`** (7 nodes): `UserDetail.php`, `UserDetail`, `.getActiveStatesStringAttribute()`, `.getCarriersStringAttribute()`, `.setActiveStatesAttribute()`, `.setCarriersAttribute()`, `.user()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 34`** (7 nodes): `components.hub-styles`, `hub.blade.php`, `hub.blade.php`, `hub.blade.php`, `hub.blade.php`, `hub.blade.php`, `hub.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 35`** (6 nodes): `CommunityAnnouncementPosted.php`, `CommunityAnnouncementPosted`, `.broadcastAs()`, `.broadcastOn()`, `.broadcastWith()`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 36`** (6 nodes): `RoleChangedMail.php`, `RoleChangedMail`, `.attachments()`, `.__construct()`, `.content()`, `.envelope()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 40`** (5 nodes): `MessageSent.php`, `MessageSent`, `.broadcastAs()`, `.broadcastOn()`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 41`** (5 nodes): `AccountCreatedMail`, `.attachments()`, `.__construct()`, `.envelope()`, `AccountCreatedMail.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 42`** (5 nodes): `TelescopeServiceProvider.php`, `TelescopeServiceProvider`, `.gate()`, `.hideSensitiveRequestDetails()`, `.register()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 43`** (5 nodes): `AppServiceProvider.php`, `AppServiceProvider`, `.boot()`, `.register()`, `.registerPermissionBladeDirectives()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 44`** (5 nodes): `NamespaceFixer.php`, `NamespaceFixer`, `.__construct()`, `.expectedNamespaceFor()`, `.fixApp()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 45`** (5 nodes): `MentionedInChatNotification.php`, `MentionedInChatNotification`, `.__construct()`, `.toArray()`, `.via()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 46`** (5 nodes): `MentionedInAnnouncementNotification.php`, `MentionedInAnnouncementNotification`, `.__construct()`, `.toArray()`, `.via()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 47`** (5 nodes): `Kernel.php`, `Kernel.php`, `Kernel`, `.commands()`, `.schedule()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 48`** (5 nodes): `Carrier.php`, `Carrier`, `.forwardedBy()`, `.lead()`, `.managedBy()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 49`** (5 nodes): `MarkAttendanceOnLogin.php`, `MarkAttendanceOnLogin`, `.__construct()`, `.handle()`, `.shouldMarkAttendance()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 50`** (5 nodes): `CheckDailyAttendance.php`, `CheckDailyAttendance`, `.__construct()`, `.handle()`, `.shouldCheckAttendance()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 51`** (5 nodes): `UpdateUserRequest.php`, `UpdateUserRequest`, `.authorize()`, `.messages()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 52`** (5 nodes): `UpdateLeadRequest.php`, `UpdateLeadRequest`, `.authorize()`, `.messages()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 53`** (5 nodes): `StoreUserRequest.php`, `StoreUserRequest`, `.authorize()`, `.messages()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 54`** (5 nodes): `UpdateAgentRequest.php`, `UpdateAgentRequest`, `.authorize()`, `.messages()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 55`** (5 nodes): `StoreAgentRequest.php`, `StoreAgentRequest`, `.authorize()`, `.messages()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 56`** (5 nodes): `StoreLedgerEntryRequest.php`, `StoreLedgerEntryRequest`, `.authorize()`, `.messages()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 57`** (5 nodes): `StoreLeadRequest.php`, `StoreLeadRequest`, `.authorize()`, `.messages()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 60`** (5 nodes): `partner.partials.carrier-filter`, `carriers.blade.php`, `dashboard-advanced.blade.php`, `ledger.blade.php`, `sales.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 61`** (4 nodes): `LeadCreated.php`, `LeadCreated`, `.broadcastOn()`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 62`** (4 nodes): `SaleCreated.php`, `SaleCreated`, `.broadcastOn()`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 63`** (4 nodes): `CarrierAliases.php`, `CarrierAliases`, `.applyFilter()`, `.applyOtherFilter()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 64`** (4 nodes): `CarrierCommissionBracket.php`, `CarrierCommissionBracket`, `.containsAge()`, `.insuranceCarrier()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 65`** (4 nodes): `ChatMessageRead.php`, `ChatMessageRead`, `.message()`, `.user()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 66`** (4 nodes): `LeadFieldHighlight.php`, `LeadFieldHighlight`, `.lead()`, `.updatedBy()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 67`** (4 nodes): `Partner.php`, `Partner`, `.carriers()`, `.carrierStates()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 68`** (4 nodes): `LocalizationController.php`, `LocalizationController`, `.__construct()`, `.lang()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 69`** (4 nodes): `drawSeries()`, `init()`, `processRawData()`, `jquery.flot.image.js`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 70`** (3 nodes): `EventServiceProvider.php`, `EventServiceProvider`, `.boot()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 71`** (3 nodes): `BroadcastServiceProvider.php`, `BroadcastServiceProvider`, `.boot()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 72`** (3 nodes): `AuthServiceProvider.php`, `AuthServiceProvider`, `.boot()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 73`** (3 nodes): `CheckModulePermissionWithRole.php`, `CheckModulePermissionWithRole`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 74`** (3 nodes): `StickyNote.php`, `StickyNote`, `.user()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 75`** (3 nodes): `EPMSExternalCost.php`, `EPMSExternalCost`, `.project()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 76`** (3 nodes): `SendLeadCreatedNotification.php`, `SendLeadCreatedNotification`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 77`** (3 nodes): `LogUserLogout.php`, `LogUserLogout`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 78`** (3 nodes): `Handler.php`, `Handler`, `.register()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 79`** (3 nodes): `TrustHosts.php`, `TrustHosts`, `.hosts()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 80`** (3 nodes): `CheckModulePermission.php`, `CheckModulePermission`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 81`** (3 nodes): `VerificationController.php`, `VerificationController`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 82`** (3 nodes): `UserObserver.php`, `UserObserver`, `.created()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 83`** (3 nodes): `plural()`, `translate()`, `is.js`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 84`** (3 nodes): `plural()`, `translate()`, `cs.js`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 85`** (3 nodes): `plural()`, `translate()`, `pl.js`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 86`** (3 nodes): `sk.js`, `plural()`, `translate()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 87`** (3 nodes): `translate()`, `verbalNumber()`, `fi.js`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 88`** (3 nodes): `plural()`, `relativeTimeWithPlural()`, `be.js`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 89`** (3 nodes): `ru.js`, `plural()`, `relativeTimeWithPlural()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 90`** (3 nodes): `admin.agents.partials.carrier-states`, `create.blade.php`, `edit.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 91`** (3 nodes): `admin.epms.partials.wbs-item`, `wbs-item.blade.php`, `show.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 95`** (3 nodes): `down()`, `up()`, `2026_04_27_120000_add_ledger_sales_return_status_to_leads_table.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 105`** (3 nodes): `down()`, `up()`, `2026_03_13_122438_add_resale_fields_to_leads_table.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 112`** (3 nodes): `RoleSeeder.php`, `RoleSeeder`, `.run()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 128`** (3 nodes): `down()`, `up()`, `2026_01_02_021649_add_beneficiaries_json_to_leads_table.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 154`** (3 nodes): `down()`, `up()`, `2026_04_06_000001_add_extra_parts_to_qa_calls.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 185`** (3 nodes): `DatabaseSeeder.php`, `DatabaseSeeder`, `.run()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 186`** (3 nodes): `InsuranceCarrierSeeder.php`, `InsuranceCarrierSeeder`, `.run()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 187`** (2 nodes): `Teams.php`, `Teams`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 188`** (2 nodes): `Statuses.php`, `Statuses`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 189`** (2 nodes): `PettyCashLedger.php`, `PettyCashLedger`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 190`** (2 nodes): `TrimStrings.php`, `TrimStrings`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 191`** (2 nodes): `TrustProxies.php`, `TrustProxies`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 192`** (2 nodes): `EncryptCookies.php`, `EncryptCookies`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 193`** (2 nodes): `PreventRequestsDuringMaintenance.php`, `PreventRequestsDuringMaintenance`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 194`** (2 nodes): `Controller.php`, `Controller`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 219`** (2 nodes): `admin.leads.index_table`, `index.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 220`** (2 nodes): `admin.partners.partials.carrier-states`, `edit.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `push()` connect `Community 3` to `Community 0`, `Community 1`, `Community 2`, `Community 4`, `Community 5`, `Community 69`, `Community 7`, `Community 8`, `Community 9`, `Community 10`, `Community 6`, `Community 12`?**
  _High betweenness centrality (0.113) - this node is a cross-community bridge._
- **Why does `min()` connect `Community 5` to `Community 0`, `Community 1`, `Community 2`, `Community 3`, `Community 4`, `Community 6`, `Community 7`, `Community 8`, `Community 10`, `Community 15`, `Community 18`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Why does `id()` connect `Community 6` to `Community 1`, `Community 10`, `Community 3`, `Community 5`?**
  _High betweenness centrality (0.041) - this node is a cross-community bridge._
- **Are the 412 inferred relationships involving `push()` (e.g. with `.createForUsers()` and `.createGroupConversation()`) actually correct?**
  _`push()` has 412 INFERRED edges - model-reasoned connections that need verification._
- **Are the 276 inferred relationships involving `min()` (e.g. with `.clampScore()` and `.getLowestQuote()`) actually correct?**
  _`min()` has 276 INFERRED edges - model-reasoned connections that need verification._
- **Are the 198 inferred relationships involving `max()` (e.g. with `.getSalesData()` and `.calculateSalary()`) actually correct?**
  _`max()` has 198 INFERRED edges - model-reasoned connections that need verification._
- **Are the 208 inferred relationships involving `count()` (e.g. with `.removeBeneficiary()` and `.collection()`) actually correct?**
  _`count()` has 208 INFERRED edges - model-reasoned connections that need verification._