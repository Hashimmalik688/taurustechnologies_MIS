# Graph Report - /var/www/taurus-crm  (2026-05-03)

## Corpus Check
- Large corpus: 1721 files · ~729,025 words. Semantic extraction will be expensive (many Claude tokens). Consider running on a subfolder, or use --no-semantic to run AST-only.

## Summary
- 3286 nodes · 5507 edges · 91 communities detected
- Extraction: 55% EXTRACTED · 45% INFERRED · 0% AMBIGUOUS · INFERRED: 2477 edges (avg confidence: 0.8)
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
- [[_COMMUNITY_Community 31|Community 31]]
- [[_COMMUNITY_Community 32|Community 32]]
- [[_COMMUNITY_Community 33|Community 33]]
- [[_COMMUNITY_Community 34|Community 34]]
- [[_COMMUNITY_Community 35|Community 35]]
- [[_COMMUNITY_Community 36|Community 36]]
- [[_COMMUNITY_Community 39|Community 39]]
- [[_COMMUNITY_Community 40|Community 40]]
- [[_COMMUNITY_Community 41|Community 41]]
- [[_COMMUNITY_Community 42|Community 42]]
- [[_COMMUNITY_Community 43|Community 43]]
- [[_COMMUNITY_Community 44|Community 44]]
- [[_COMMUNITY_Community 45|Community 45]]
- [[_COMMUNITY_Community 46|Community 46]]
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
- [[_COMMUNITY_Community 83|Community 83]]
- [[_COMMUNITY_Community 84|Community 84]]
- [[_COMMUNITY_Community 223|Community 223]]
- [[_COMMUNITY_Community 224|Community 224]]
- [[_COMMUNITY_Community 225|Community 225]]
- [[_COMMUNITY_Community 226|Community 226]]
- [[_COMMUNITY_Community 227|Community 227]]
- [[_COMMUNITY_Community 228|Community 228]]
- [[_COMMUNITY_Community 229|Community 229]]
- [[_COMMUNITY_Community 230|Community 230]]
- [[_COMMUNITY_Community 231|Community 231]]
- [[_COMMUNITY_Community 232|Community 232]]
- [[_COMMUNITY_Community 236|Community 236]]
- [[_COMMUNITY_Community 237|Community 237]]

## God Nodes (most connected - your core abstractions)
1. `save` - 84 edges
2. `partials.pipeline-dashboard-styles` - 55 edges
3. `EPMSProject` - 54 edges
4. `Lead` - 51 edges
5. `Attendance` - 50 edges
6. `QaCall` - 43 edges
7. `LeadController` - 42 edges
8. `AuditLog` - 35 edges
9. `Module` - 35 edges
10. `QADashboardController` - 34 edges

## Surprising Connections (you probably didn't know these)
- `up()` --calls--> `Module`  [INFERRED]
  /var/www/taurus-crm/database/migrations/2026_04_13_000002_seed_carrier_sheet_rates.php → /var/www/taurus-crm/app/Models/Module.php
- `down()` --calls--> `Module`  [INFERRED]
  /var/www/taurus-crm/database/migrations/2026_02_23_000002_remove_dead_holidays_notifications_modules.php → /var/www/taurus-crm/app/Models/Module.php
- `up()` --calls--> `Module`  [INFERRED]
  /var/www/taurus-crm/database/migrations/2026_04_10_000001_add_qa_scoring_module.php → /var/www/taurus-crm/app/Models/Module.php
- `up()` --calls--> `Module`  [INFERRED]
  /var/www/taurus-crm/database/migrations/2026_03_20_000001_add_permission_manager_module.php → /var/www/taurus-crm/app/Models/Module.php
- `up()` --calls--> `UserModulePermission`  [INFERRED]
  /var/www/taurus-crm/database/migrations/2026_02_23_000002_remove_dead_holidays_notifications_modules.php → /var/www/taurus-crm/app/Models/UserModulePermission.php

## Communities

### Community 0 - "Community 0"
Cohesion: 0.0
Nodes (33): up(), up(), up(), up(), up(), addBeneficiary, AuditLog, BadLead (+25 more)

### Community 1 - "Community 1"
Cohesion: 0.0
Nodes (24): AnalyticsController, AnalyticsService, AuditLogController, ChargebackController, ChartOfAccount, ChartOfAccountController, CloserReportController, DashboardController (+16 more)

### Community 2 - "Community 2"
Cohesion: 0.0
Nodes (68): down(), getConnection(), up(), up(), up(), up(), up(), up() (+60 more)

### Community 3 - "Community 3"
Cohesion: 0.0
Nodes (36): down(), up(), down(), up(), down(), down(), down(), up() (+28 more)

### Community 4 - "Community 4"
Cohesion: 0.0
Nodes (14): EPMSAiPlan, EPMSComment, EPMSDocument, EPMSExternalCost, EPMSMilestone, EPMSProject, EPMSProjectController, EPMSProjectMember (+6 more)

### Community 5 - "Community 5"
Cohesion: 0.0
Nodes (22): getChartColorsArray(), AssemblyAIService, getChartColorsArray(), ClaudeService, getChartColorsArray(), getChartColorsArray(), getChartColorsArray(), DownloadAndProcessRecording (+14 more)

### Community 6 - "Community 6"
Cohesion: 0.0
Nodes (25): down(), up(), down(), down(), down(), down(), down(), AgentController (+17 more)

### Community 7 - "Community 7"
Cohesion: 0.0
Nodes (14): Attendance, AttendanceController, AttendanceService, AutoCheckoutAttendance, CheckDailyAttendance, DashboardApiController, EmployeeDashboardController, Holiday (+6 more)

### Community 8 - "Community 8"
Cohesion: 0.0
Nodes (26): down(), up(), down(), up(), down(), up(), down(), up() (+18 more)

### Community 9 - "Community 9"
Cohesion: 0.0
Nodes (15): CleanupAnalyze, CleanupFormat, CleanupKit, CleanupPsr4, FixAttendanceWorkingHours, GeminiService, ImportEMSDataSeeder, LedgerJournalEntry (+7 more)

### Community 10 - "Community 10"
Cohesion: 0.0
Nodes (7): Notification, NotificationController, NotificationService, SendLeadCreatedNotification, SendSaleCreatedNotification, SidebarHelper, User

### Community 11 - "Community 11"
Cohesion: 0.0
Nodes (19): getLanguage(), init(), initActiveMenu(), initCheckAll(), initComponents(), initDropdownMenu(), initFullScreen(), initHoriMenuActive() (+11 more)

### Community 12 - "Community 12"
Cohesion: 0.0
Nodes (9): up(), ChatAttachment, ChatBackup, ChatController, ChatConversation, ChatMessage, ChatParticipant, ChatShadowController (+1 more)

### Community 13 - "Community 13"
Cohesion: 0.0
Nodes (12): AccountSwitchingDetector, AgentDashboardController, ChatNotificationController, ChatNotificationPreference, HomeController, LocalizationController, LoginController, NotepadController (+4 more)

### Community 14 - "Community 14"
Cohesion: 0.0
Nodes (7): SyncZoomCallLogs, SyncZoomRecordings, ZoomOAuthService, ZoomPhoneApiService, ZoomPhoneEmbedController, ZoomPhoneReportProbe, ZoomWebhookLog

### Community 15 - "Community 15"
Cohesion: 0.0
Nodes (5): CarrierSheetController, CarrierSheetEntry, CarrierSheetOpeningCb, CarrierSheetRate, CarrierSheetService

### Community 16 - "Community 16"
Cohesion: 0.0
Nodes (4): partials.custom-select-datepicker-styles, partials.pipeline-dashboard-styles, partials.sl-filter-assets, peregrine.closers.form

### Community 17 - "Community 17"
Cohesion: 0.0
Nodes (5): PabsProject, PabsProjectApproval, PabsProjectComment, ProjectAuthorizationService, ProjectController

### Community 18 - "Community 18"
Cohesion: 0.0
Nodes (7): AgentCarrierCommission, AgentCarrierState, CommissionCalculationService, InsuranceCarrier, InsuranceCarrierController, getDashboardStats(), PartnerRevenueService

### Community 19 - "Community 19"
Cohesion: 0.0
Nodes (3): PabsTicket, PabsTicketComment, TicketController

### Community 20 - "Community 20"
Cohesion: 0.0
Nodes (13): changeNewScheduleCalendar(), getDataAction(), onChangeCalendars(), onChangeNewScheduleCalendar(), onClickMenu(), onClickNavi(), refreshScheduleVisibility(), saveNewSchedule() (+5 more)

### Community 21 - "Community 21"
Cohesion: 0.0
Nodes (4): AllowedDevice, DeviceApprove, DeviceController, RestrictToAllowedDevice

### Community 22 - "Community 22"
Cohesion: 0.0
Nodes (4): Announcement, AnnouncementController, AnnouncementSeeder, CleanupEmptyCommunities

### Community 23 - "Community 23"
Cohesion: 0.0
Nodes (15): editContactList(), fetchIdFromObj(), findNextId(), loadUserList(), removeItem(), filterData(), isStatus(), isType() (+7 more)

### Community 24 - "Community 24"
Cohesion: 0.0
Nodes (1): admin.accounting._nav

### Community 25 - "Community 25"
Cohesion: 0.0
Nodes (1): SalaryRecord

### Community 26 - "Community 26"
Cohesion: 0.0
Nodes (4): PartnerAuthController, PartnerAuthenticate, PreventPartnerAccess, PreventUserAccess

### Community 27 - "Community 27"
Cohesion: 0.0
Nodes (2): DupeCheckerController, LeadDeduplicationService

### Community 28 - "Community 28"
Cohesion: 0.0
Nodes (1): FileUploadService

### Community 29 - "Community 29"
Cohesion: 0.0
Nodes (1): PartnerLedgerRepository

### Community 30 - "Community 30"
Cohesion: 0.0
Nodes (2): GoogleSheetsService, SyncSaleToGoogleSheets

### Community 31 - "Community 31"
Cohesion: 0.0
Nodes (2): ImportSanitizer, LeadsImport

### Community 32 - "Community 32"
Cohesion: 0.0
Nodes (1): AnnouncementPolicy

### Community 33 - "Community 33"
Cohesion: 0.0
Nodes (1): CallLog

### Community 34 - "Community 34"
Cohesion: 0.0
Nodes (1): Community

### Community 35 - "Community 35"
Cohesion: 0.0
Nodes (1): QaResult

### Community 36 - "Community 36"
Cohesion: 0.0
Nodes (6): components.freeloaders-widget, components.sticky-notes, components.zoom-phone-widget, layouts.head-css, layouts.sidebar, layouts.vendor-scripts

### Community 39 - "Community 39"
Cohesion: 0.0
Nodes (1): UserDetail

### Community 40 - "Community 40"
Cohesion: 0.0
Nodes (1): components.hub-styles

### Community 41 - "Community 41"
Cohesion: 0.0
Nodes (1): MessageSent

### Community 42 - "Community 42"
Cohesion: 0.0
Nodes (1): CommunityAnnouncementPosted

### Community 43 - "Community 43"
Cohesion: 0.0
Nodes (1): CallStatusChanged

### Community 44 - "Community 44"
Cohesion: 0.0
Nodes (1): MessageRead

### Community 45 - "Community 45"
Cohesion: 0.0
Nodes (1): AccountCreatedMail

### Community 46 - "Community 46"
Cohesion: 0.0
Nodes (1): RoleChangedMail

### Community 48 - "Community 48"
Cohesion: 0.0
Nodes (1): UpdateLeadRequest

### Community 49 - "Community 49"
Cohesion: 0.0
Nodes (1): StoreLeadRequest

### Community 50 - "Community 50"
Cohesion: 0.0
Nodes (1): TelescopeServiceProvider

### Community 51 - "Community 51"
Cohesion: 0.0
Nodes (1): AppServiceProvider

### Community 52 - "Community 52"
Cohesion: 0.0
Nodes (1): CarrierAliases

### Community 53 - "Community 53"
Cohesion: 0.0
Nodes (1): Kernel

### Community 54 - "Community 54"
Cohesion: 0.0
Nodes (1): Carrier

### Community 55 - "Community 55"
Cohesion: 0.0
Nodes (1): UpdateUserRequest

### Community 56 - "Community 56"
Cohesion: 0.0
Nodes (1): StoreUserRequest

### Community 57 - "Community 57"
Cohesion: 0.0
Nodes (1): UpdateAgentRequest

### Community 58 - "Community 58"
Cohesion: 0.0
Nodes (1): StoreAgentRequest

### Community 59 - "Community 59"
Cohesion: 0.0
Nodes (1): StoreLedgerEntryRequest

### Community 60 - "Community 60"
Cohesion: 0.0
Nodes (1): partner.partials.carrier-filter

### Community 61 - "Community 61"
Cohesion: 0.0
Nodes (1): LeadCreated

### Community 62 - "Community 62"
Cohesion: 0.0
Nodes (1): SaleCreated

### Community 63 - "Community 63"
Cohesion: 0.0
Nodes (1): RepositoryServiceProvider

### Community 64 - "Community 64"
Cohesion: 0.0
Nodes (1): CarrierCommissionBracket

### Community 65 - "Community 65"
Cohesion: 0.0
Nodes (1): ChatMessageRead

### Community 66 - "Community 66"
Cohesion: 0.0
Nodes (1): ChatMessageReaction

### Community 67 - "Community 67"
Cohesion: 0.0
Nodes (1): Partner

### Community 68 - "Community 68"
Cohesion: 0.0
Nodes (1): LeadObserver

### Community 71 - "Community 71"
Cohesion: 0.0
Nodes (1): CreateFailedJobsTable

### Community 72 - "Community 72"
Cohesion: 0.0
Nodes (1): CreateUsersTable

### Community 73 - "Community 73"
Cohesion: 0.0
Nodes (1): EventServiceProvider

### Community 74 - "Community 74"
Cohesion: 0.0
Nodes (1): BroadcastServiceProvider

### Community 75 - "Community 75"
Cohesion: 0.0
Nodes (1): AuthServiceProvider

### Community 76 - "Community 76"
Cohesion: 0.0
Nodes (1): FileUtils

### Community 77 - "Community 77"
Cohesion: 0.0
Nodes (1): SetupGoogleSheetHeaders

### Community 78 - "Community 78"
Cohesion: 0.0
Nodes (1): Handler

### Community 79 - "Community 79"
Cohesion: 0.0
Nodes (1): TrustHosts

### Community 80 - "Community 80"
Cohesion: 0.0
Nodes (1): RedirectIfAuthenticated

### Community 81 - "Community 81"
Cohesion: 0.0
Nodes (1): Authenticate

### Community 83 - "Community 83"
Cohesion: 0.0
Nodes (1): admin.agents.partials.carrier-states

### Community 84 - "Community 84"
Cohesion: 0.0
Nodes (1): admin.epms.partials.wbs-item

### Community 223 - "Community 223"
Cohesion: 0.0
Nodes (1): DatabaseSeeder

### Community 224 - "Community 224"
Cohesion: 0.0
Nodes (1): Teams

### Community 225 - "Community 225"
Cohesion: 0.0
Nodes (1): Statuses

### Community 226 - "Community 226"
Cohesion: 0.0
Nodes (1): PettyCashLedger

### Community 227 - "Community 227"
Cohesion: 0.0
Nodes (1): TrimStrings

### Community 228 - "Community 228"
Cohesion: 0.0
Nodes (1): TrustProxies

### Community 229 - "Community 229"
Cohesion: 0.0
Nodes (1): EncryptCookies

### Community 230 - "Community 230"
Cohesion: 0.0
Nodes (1): PreventRequestsDuringMaintenance

### Community 231 - "Community 231"
Cohesion: 0.0
Nodes (1): Controller

### Community 232 - "Community 232"
Cohesion: 0.0
Nodes (1): Build a community-aggregate graph (one node per community) and generate graph.ht

### Community 236 - "Community 236"
Cohesion: 0.0
Nodes (1): admin.leads.index_table

### Community 237 - "Community 237"
Cohesion: 0.0
Nodes (1): admin.partners.partials.carrier-states

## Knowledge Gaps
- **17 isolated node(s):** `Teams`, `Statuses`, `PettyCashLedger`, `TrimStrings`, `TrustProxies` (+12 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **Thin community `Community 24`** (19 nodes): `admin.accounting._nav`, `dashboard.blade.php`, `create.blade.php`, `index.blade.php`, `show.blade.php`, `carrier-show.blade.php`, `index.blade.php`, `overview.blade.php`, `chargeback.blade.php`, `opening-balance.blade.php`, `payment.blade.php`, `sale.blade.php`, `balance-sheet.blade.php`, `expense-tracker.blade.php`, `profit-loss.blade.php`, `trial-balance.blade.php`, `index.blade.php`, `partner.blade.php`, `returns.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 25`** (16 nodes): `SalaryRecord.php`, `SalaryRecord`, `.basicComponent()`, `.bonusComponent()`, `.components()`, `.deductions()`, `.dockRecords()`, `.getAttendancePercentageAttribute()`, `.getAttendanceSummaryAttribute()`, `.getHasPerfectAttendanceAttribute()`, `.getMonthNameAttribute()`, `.getNetAttendanceImpactAttribute()`, `.getPunctualityPercentageAttribute()`, `.getSandwichPenaltyDaysAttribute()`, `.getTotalSalaryAdjustmentAttribute()`, `.user()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 27`** (14 nodes): `DupeCheckerController.php`, `LeadDeduplicationService.php`, `DupeCheckerController`, `.__construct()`, `.exportComparisonToCsv()`, `.exportDuplicatesToCsv()`, `.fileComparison()`, `.index()`, `.normalizePhoneNumber()`, `.runDeduplication()`, `.selfCheck()`, `LeadDeduplicationService`, `.deduplicateByPhone()`, `.mergeLeadData()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 28`** (13 nodes): `FileUploadService.php`, `FileUploadService`, `.deleteFile()`, `.fileExists()`, `.generateUniqueFilename()`, `.getAllowedMimes()`, `.getFileSizeMB()`, `.getFileUrl()`, `.uploadAvatar()`, `.uploadDocument()`, `.uploadFile()`, `.validateAndStore()`, `.validateMimeType()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 29`** (12 nodes): `PartnerLedgerRepository.php`, `PartnerLedgerRepository`, `.getARAccount()`, `.getBalance()`, `.getBalanceAging()`, `.getChargebacksSummary()`, `.getDashboardStats()`, `.getLedger()`, `.getLedgerByCarrier()`, `.getPaymentsSummary()`, `.getSalesSummary()`, `.isCarrierMatch()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 30`** (11 nodes): `SyncSaleToGoogleSheets.php`, `GoogleSheetsService.php`, `GoogleSheetsService`, `.appendSale()`, `.buildPayload()`, `.__construct()`, `SyncSaleToGoogleSheets`, `.backoff()`, `.__construct()`, `.failed()`, `.handle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 31`** (11 nodes): `LeadsImport.php`, `ImportSanitizer.php`, `ImportSanitizer`, `.parseExcelDate()`, `.parseMoney()`, `LeadsImport`, `.collection()`, `.getValueFromRow()`, `.normalizePhoneNumber()`, `.parseExcelDate()`, `.parseMoney()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 32`** (9 nodes): `AnnouncementPolicy`, `.create()`, `.delete()`, `.forceDelete()`, `.isAuthorized()`, `.restore()`, `.update()`, `.viewAny()`, `AnnouncementPolicy.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 33`** (9 nodes): `CallLog.php`, `CallLog`, `.agent()`, `.creator()`, `.getFormattedDurationAttribute()`, `.lead()`, `.scopeByAgent()`, `.scopeNeedsFollowUp()`, `.scopeSuccessful()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 34`** (8 nodes): `Community.php`, `Community`, `.announcements()`, `.chatConversations()`, `.communityAnnouncements()`, `.createdByManagers()`, `.creator()`, `.members()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 35`** (8 nodes): `QaResult.php`, `QaResult`, `.complianceFlags()`, `.getComplianceChecksAttribute()`, `.getScoreBreakdownAttribute()`, `.qaCall()`, `.scopeComplianceFail()`, `.scopeVoidRisk()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 39`** (7 nodes): `UserDetail.php`, `UserDetail`, `.getActiveStatesStringAttribute()`, `.getCarriersStringAttribute()`, `.setActiveStatesAttribute()`, `.setCarriersAttribute()`, `.user()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 40`** (7 nodes): `components.hub-styles`, `hub.blade.php`, `hub.blade.php`, `hub.blade.php`, `hub.blade.php`, `hub.blade.php`, `hub.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 41`** (6 nodes): `MessageSent.php`, `MessageSent`, `.broadcastAs()`, `.broadcastOn()`, `.broadcastWith()`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 42`** (6 nodes): `CommunityAnnouncementPosted.php`, `CommunityAnnouncementPosted`, `.broadcastAs()`, `.broadcastOn()`, `.broadcastWith()`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 43`** (6 nodes): `CallStatusChanged.php`, `CallStatusChanged`, `.broadcastOn()`, `.broadcastWith()`, `.__construct()`, `.sanitizePhoneNumber()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 44`** (6 nodes): `MessageRead.php`, `MessageRead`, `.broadcastAs()`, `.broadcastOn()`, `.broadcastWith()`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 45`** (6 nodes): `AccountCreatedMail`, `.attachments()`, `.__construct()`, `.content()`, `.envelope()`, `AccountCreatedMail.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 46`** (6 nodes): `RoleChangedMail.php`, `RoleChangedMail`, `.attachments()`, `.__construct()`, `.content()`, `.envelope()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 48`** (6 nodes): `UpdateLeadRequest.php`, `UpdateLeadRequest`, `.authorize()`, `.messages()`, `.prepareForValidation()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 49`** (6 nodes): `StoreLeadRequest.php`, `StoreLeadRequest`, `.authorize()`, `.messages()`, `.prepareForValidation()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 50`** (5 nodes): `TelescopeServiceProvider.php`, `TelescopeServiceProvider`, `.gate()`, `.hideSensitiveRequestDetails()`, `.register()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 51`** (5 nodes): `AppServiceProvider.php`, `AppServiceProvider`, `.boot()`, `.register()`, `.registerPermissionBladeDirectives()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 52`** (5 nodes): `CarrierAliases.php`, `CarrierAliases`, `.applyFilter()`, `.applyOtherFilter()`, `.resolve()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 53`** (5 nodes): `Kernel.php`, `Kernel.php`, `Kernel`, `.commands()`, `.schedule()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 54`** (5 nodes): `Carrier.php`, `Carrier`, `.forwardedBy()`, `.lead()`, `.managedBy()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 55`** (5 nodes): `UpdateUserRequest.php`, `UpdateUserRequest`, `.authorize()`, `.messages()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 56`** (5 nodes): `StoreUserRequest.php`, `StoreUserRequest`, `.authorize()`, `.messages()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 57`** (5 nodes): `UpdateAgentRequest.php`, `UpdateAgentRequest`, `.authorize()`, `.messages()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 58`** (5 nodes): `StoreAgentRequest.php`, `StoreAgentRequest`, `.authorize()`, `.messages()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 59`** (5 nodes): `StoreLedgerEntryRequest.php`, `StoreLedgerEntryRequest`, `.authorize()`, `.messages()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 60`** (5 nodes): `partner.partials.carrier-filter`, `carriers.blade.php`, `dashboard-advanced.blade.php`, `ledger.blade.php`, `sales.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 61`** (4 nodes): `LeadCreated.php`, `LeadCreated`, `.broadcastOn()`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 62`** (4 nodes): `SaleCreated.php`, `SaleCreated`, `.broadcastOn()`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 63`** (4 nodes): `RepositoryServiceProvider.php`, `RepositoryServiceProvider`, `.boot()`, `.register()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 64`** (4 nodes): `CarrierCommissionBracket.php`, `CarrierCommissionBracket`, `.containsAge()`, `.insuranceCarrier()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 65`** (4 nodes): `ChatMessageRead.php`, `ChatMessageRead`, `.message()`, `.user()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 66`** (4 nodes): `ChatMessageReaction.php`, `ChatMessageReaction`, `.message()`, `.user()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 67`** (4 nodes): `Partner.php`, `Partner`, `.carriers()`, `.carrierStates()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 68`** (4 nodes): `LeadObserver.php`, `LeadObserver`, `.resolveSettlementType()`, `.updating()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 71`** (4 nodes): `CreateFailedJobsTable`, `.down()`, `.up()`, `2019_08_19_000000_create_failed_jobs_table.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 72`** (4 nodes): `CreateUsersTable`, `.down()`, `.up()`, `2014_10_12_000000_create_users_table.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 73`** (3 nodes): `EventServiceProvider.php`, `EventServiceProvider`, `.boot()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 74`** (3 nodes): `BroadcastServiceProvider.php`, `BroadcastServiceProvider`, `.boot()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 75`** (3 nodes): `AuthServiceProvider.php`, `AuthServiceProvider`, `.boot()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 76`** (3 nodes): `FileUtils.php`, `FileUtils`, `.humanBytes()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 77`** (3 nodes): `SetupGoogleSheetHeaders.php`, `SetupGoogleSheetHeaders`, `.handle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 78`** (3 nodes): `Handler.php`, `Handler`, `.register()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 79`** (3 nodes): `TrustHosts.php`, `TrustHosts`, `.hosts()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 80`** (3 nodes): `RedirectIfAuthenticated.php`, `RedirectIfAuthenticated`, `.handle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 81`** (3 nodes): `Authenticate.php`, `Authenticate`, `.redirectTo()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 83`** (3 nodes): `admin.agents.partials.carrier-states`, `create.blade.php`, `edit.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 84`** (3 nodes): `admin.epms.partials.wbs-item`, `wbs-item.blade.php`, `show.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 223`** (3 nodes): `DatabaseSeeder.php`, `DatabaseSeeder`, `.run()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 224`** (2 nodes): `Teams.php`, `Teams`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 225`** (2 nodes): `Statuses.php`, `Statuses`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 226`** (2 nodes): `PettyCashLedger.php`, `PettyCashLedger`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 227`** (2 nodes): `TrimStrings.php`, `TrimStrings`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 228`** (2 nodes): `TrustProxies.php`, `TrustProxies`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 229`** (2 nodes): `EncryptCookies.php`, `EncryptCookies`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 230`** (2 nodes): `PreventRequestsDuringMaintenance.php`, `PreventRequestsDuringMaintenance`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 231`** (2 nodes): `Controller.php`, `Controller`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 232`** (2 nodes): `graphify_viz.py`, `Build a community-aggregate graph (one node per community) and generate graph.ht`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 236`** (2 nodes): `admin.leads.index_table`, `index.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 237`** (2 nodes): `admin.partners.partials.carrier-states`, `edit.blade.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.