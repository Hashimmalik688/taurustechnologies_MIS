# Community 27
**14 nodes**

## Members
- [[app_http_controllers_admin_dupecheckercontroller_php]]
- [[app_services_leaddeduplicationservice_php]]
- [[dupecheckercontroller_dupecheckercontroller]]
- [[dupecheckercontroller_dupecheckercontroller_construct]]
- [[dupecheckercontroller_dupecheckercontroller_exportcomparisontocsv]]
- [[dupecheckercontroller_dupecheckercontroller_exportduplicatestocsv]]
- [[dupecheckercontroller_dupecheckercontroller_filecomparison]]
- [[dupecheckercontroller_dupecheckercontroller_index]]
- [[dupecheckercontroller_dupecheckercontroller_normalizephonenumber]]
- [[dupecheckercontroller_dupecheckercontroller_rundeduplication]]
- [[dupecheckercontroller_dupecheckercontroller_selfcheck]]
- [[leaddeduplicationservice_leaddeduplicationservice]]
- [[leaddeduplicationservice_leaddeduplicationservice_deduplicatebyphone]]
- [[leaddeduplicationservice_leaddeduplicationservice_mergeleaddata]]

## Internal connections
- [[app_services_leaddeduplicationservice_php]] —contains→ [[leaddeduplicationservice_leaddeduplicationservice]] `EXTRACTED`
- [[leaddeduplicationservice_leaddeduplicationservice]] —method→ [[leaddeduplicationservice_leaddeduplicationservice_deduplicatebyphone]] `EXTRACTED`
- [[leaddeduplicationservice_leaddeduplicationservice]] —method→ [[leaddeduplicationservice_leaddeduplicationservice_mergeleaddata]] `EXTRACTED`
- [[leaddeduplicationservice_leaddeduplicationservice_deduplicatebyphone]] —calls→ [[leaddeduplicationservice_leaddeduplicationservice_mergeleaddata]] `EXTRACTED`
- [[leaddeduplicationservice_leaddeduplicationservice_deduplicatebyphone]] —calls→ [[dupecheckercontroller_dupecheckercontroller_rundeduplication]] `INFERRED`
- [[app_http_controllers_admin_dupecheckercontroller_php]] —contains→ [[dupecheckercontroller_dupecheckercontroller]] `EXTRACTED`
- [[dupecheckercontroller_dupecheckercontroller]] —method→ [[dupecheckercontroller_dupecheckercontroller_construct]] `EXTRACTED`
- [[dupecheckercontroller_dupecheckercontroller]] —method→ [[dupecheckercontroller_dupecheckercontroller_index]] `EXTRACTED`
- [[dupecheckercontroller_dupecheckercontroller]] —method→ [[dupecheckercontroller_dupecheckercontroller_selfcheck]] `EXTRACTED`
- [[dupecheckercontroller_dupecheckercontroller]] —method→ [[dupecheckercontroller_dupecheckercontroller_filecomparison]] `EXTRACTED`
- [[dupecheckercontroller_dupecheckercontroller]] —method→ [[dupecheckercontroller_dupecheckercontroller_rundeduplication]] `EXTRACTED`
- [[dupecheckercontroller_dupecheckercontroller]] —method→ [[dupecheckercontroller_dupecheckercontroller_exportduplicatestocsv]] `EXTRACTED`
- [[dupecheckercontroller_dupecheckercontroller]] —method→ [[dupecheckercontroller_dupecheckercontroller_exportcomparisontocsv]] `EXTRACTED`
- [[dupecheckercontroller_dupecheckercontroller]] —method→ [[dupecheckercontroller_dupecheckercontroller_normalizephonenumber]] `EXTRACTED`
- [[dupecheckercontroller_dupecheckercontroller_selfcheck]] —calls→ [[dupecheckercontroller_dupecheckercontroller_exportduplicatestocsv]] `EXTRACTED`
- [[dupecheckercontroller_dupecheckercontroller_filecomparison]] —calls→ [[dupecheckercontroller_dupecheckercontroller_normalizephonenumber]] `EXTRACTED`
- [[dupecheckercontroller_dupecheckercontroller_filecomparison]] —calls→ [[dupecheckercontroller_dupecheckercontroller_exportcomparisontocsv]] `EXTRACTED`
