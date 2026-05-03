# Community 18
**23 nodes**

## Members
- [[app_console_commands_cleanupanalyze_php]]
- [[app_services_fileuploadservice_php]]
- [[cleanupanalyze_cleanupanalyze]]
- [[cleanupanalyze_cleanupanalyze_finddanglingviews]]
- [[cleanupanalyze_cleanupanalyze_findsuspiciouslargefiles]]
- [[cleanupanalyze_cleanupanalyze_findunreferencedcontrollers]]
- [[cleanupanalyze_cleanupanalyze_handle]]
- [[cleanupanalyze_cleanupanalyze_section]]
- [[echarts_esm_confinetooltipposition]]
- [[echarts_esm_getcomputedstyle]]
- [[echarts_esm_getsize]]
- [[echarts_esm_parseint10]]
- [[echarts_esm_refixtooltipposition]]
- [[fileuploadservice_fileuploadservice]]
- [[fileuploadservice_fileuploadservice_fileexists]]
- [[fileuploadservice_fileuploadservice_generateuniquefilename]]
- [[fileuploadservice_fileuploadservice_getallowedmimes]]
- [[fileuploadservice_fileuploadservice_getfileurl]]
- [[fileuploadservice_fileuploadservice_uploadavatar]]
- [[fileuploadservice_fileuploadservice_uploaddocument]]
- [[fileuploadservice_fileuploadservice_uploadfile]]
- [[fileuploadservice_fileuploadservice_validateandstore]]
- [[fileuploadservice_fileuploadservice_validatemimetype]]

## Internal connections
- [[app_services_fileuploadservice_php]] —contains→ [[fileuploadservice_fileuploadservice]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice]] —method→ [[fileuploadservice_fileuploadservice_uploadavatar]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice]] —method→ [[fileuploadservice_fileuploadservice_uploaddocument]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice]] —method→ [[fileuploadservice_fileuploadservice_uploadfile]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice]] —method→ [[fileuploadservice_fileuploadservice_validateandstore]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice]] —method→ [[fileuploadservice_fileuploadservice_validatemimetype]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice]] —method→ [[fileuploadservice_fileuploadservice_getallowedmimes]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice]] —method→ [[fileuploadservice_fileuploadservice_generateuniquefilename]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice]] —method→ [[fileuploadservice_fileuploadservice_getfileurl]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice]] —method→ [[fileuploadservice_fileuploadservice_fileexists]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice_uploadavatar]] —calls→ [[fileuploadservice_fileuploadservice_validateandstore]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice_uploaddocument]] —calls→ [[fileuploadservice_fileuploadservice_validateandstore]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice_uploadfile]] —calls→ [[fileuploadservice_fileuploadservice_validateandstore]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice_validateandstore]] —calls→ [[fileuploadservice_fileuploadservice_validatemimetype]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice_validateandstore]] —calls→ [[fileuploadservice_fileuploadservice_generateuniquefilename]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice_validateandstore]] —calls→ [[echarts_esm_getsize]] `INFERRED`
- [[fileuploadservice_fileuploadservice_validatemimetype]] —calls→ [[fileuploadservice_fileuploadservice_getallowedmimes]] `EXTRACTED`
- [[app_console_commands_cleanupanalyze_php]] —contains→ [[cleanupanalyze_cleanupanalyze]] `EXTRACTED`
- [[cleanupanalyze_cleanupanalyze]] —method→ [[cleanupanalyze_cleanupanalyze_handle]] `EXTRACTED`
- [[cleanupanalyze_cleanupanalyze]] —method→ [[cleanupanalyze_cleanupanalyze_section]] `EXTRACTED`
- [[cleanupanalyze_cleanupanalyze]] —method→ [[cleanupanalyze_cleanupanalyze_findunreferencedcontrollers]] `EXTRACTED`
- [[cleanupanalyze_cleanupanalyze]] —method→ [[cleanupanalyze_cleanupanalyze_finddanglingviews]] `EXTRACTED`
- [[cleanupanalyze_cleanupanalyze]] —method→ [[cleanupanalyze_cleanupanalyze_findsuspiciouslargefiles]] `EXTRACTED`
- [[cleanupanalyze_cleanupanalyze_handle]] —calls→ [[cleanupanalyze_cleanupanalyze_findunreferencedcontrollers]] `EXTRACTED`
- [[cleanupanalyze_cleanupanalyze_handle]] —calls→ [[cleanupanalyze_cleanupanalyze_finddanglingviews]] `EXTRACTED`
- [[cleanupanalyze_cleanupanalyze_handle]] —calls→ [[cleanupanalyze_cleanupanalyze_findsuspiciouslargefiles]] `EXTRACTED`
- [[cleanupanalyze_cleanupanalyze_handle]] —calls→ [[cleanupanalyze_cleanupanalyze_section]] `EXTRACTED`
- [[cleanupanalyze_cleanupanalyze_findsuspiciouslargefiles]] —calls→ [[echarts_esm_getsize]] `INFERRED`
- [[echarts_esm_parseint10]] —calls→ [[echarts_esm_getsize]] `EXTRACTED`
- [[echarts_esm_getsize]] —calls→ [[echarts_esm_getcomputedstyle]] `EXTRACTED`
