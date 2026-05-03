# Community 30
**11 nodes**

## Members
- [[app_jobs_syncsaletogooglesheets_php]]
- [[app_services_googlesheetsservice_php]]
- [[googlesheetsservice_googlesheetsservice]]
- [[googlesheetsservice_googlesheetsservice_appendsale]]
- [[googlesheetsservice_googlesheetsservice_buildpayload]]
- [[googlesheetsservice_googlesheetsservice_construct]]
- [[syncsaletogooglesheets_syncsaletogooglesheets]]
- [[syncsaletogooglesheets_syncsaletogooglesheets_backoff]]
- [[syncsaletogooglesheets_syncsaletogooglesheets_construct]]
- [[syncsaletogooglesheets_syncsaletogooglesheets_failed]]
- [[syncsaletogooglesheets_syncsaletogooglesheets_handle]]

## Internal connections
- [[app_jobs_syncsaletogooglesheets_php]] —contains→ [[syncsaletogooglesheets_syncsaletogooglesheets]] `EXTRACTED`
- [[syncsaletogooglesheets_syncsaletogooglesheets]] —method→ [[syncsaletogooglesheets_syncsaletogooglesheets_backoff]] `EXTRACTED`
- [[syncsaletogooglesheets_syncsaletogooglesheets]] —method→ [[syncsaletogooglesheets_syncsaletogooglesheets_construct]] `EXTRACTED`
- [[syncsaletogooglesheets_syncsaletogooglesheets]] —method→ [[syncsaletogooglesheets_syncsaletogooglesheets_handle]] `EXTRACTED`
- [[syncsaletogooglesheets_syncsaletogooglesheets]] —method→ [[syncsaletogooglesheets_syncsaletogooglesheets_failed]] `EXTRACTED`
- [[syncsaletogooglesheets_syncsaletogooglesheets_handle]] —calls→ [[googlesheetsservice_googlesheetsservice_appendsale]] `INFERRED`
- [[app_services_googlesheetsservice_php]] —contains→ [[googlesheetsservice_googlesheetsservice]] `EXTRACTED`
- [[googlesheetsservice_googlesheetsservice]] —method→ [[googlesheetsservice_googlesheetsservice_construct]] `EXTRACTED`
- [[googlesheetsservice_googlesheetsservice]] —method→ [[googlesheetsservice_googlesheetsservice_appendsale]] `EXTRACTED`
- [[googlesheetsservice_googlesheetsservice]] —method→ [[googlesheetsservice_googlesheetsservice_buildpayload]] `EXTRACTED`
- [[googlesheetsservice_googlesheetsservice_appendsale]] —calls→ [[googlesheetsservice_googlesheetsservice_buildpayload]] `EXTRACTED`
