# Community 28
**13 nodes**

## Members
- [[app_services_fileuploadservice_php]]
- [[fileuploadservice_fileuploadservice]]
- [[fileuploadservice_fileuploadservice_deletefile]]
- [[fileuploadservice_fileuploadservice_fileexists]]
- [[fileuploadservice_fileuploadservice_generateuniquefilename]]
- [[fileuploadservice_fileuploadservice_getallowedmimes]]
- [[fileuploadservice_fileuploadservice_getfilesizemb]]
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
- [[fileuploadservice_fileuploadservice]] —method→ [[fileuploadservice_fileuploadservice_deletefile]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice]] —method→ [[fileuploadservice_fileuploadservice_getfileurl]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice]] —method→ [[fileuploadservice_fileuploadservice_fileexists]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice]] —method→ [[fileuploadservice_fileuploadservice_getfilesizemb]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice_uploadavatar]] —calls→ [[fileuploadservice_fileuploadservice_validateandstore]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice_uploaddocument]] —calls→ [[fileuploadservice_fileuploadservice_validateandstore]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice_uploadfile]] —calls→ [[fileuploadservice_fileuploadservice_validateandstore]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice_validateandstore]] —calls→ [[fileuploadservice_fileuploadservice_validatemimetype]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice_validateandstore]] —calls→ [[fileuploadservice_fileuploadservice_generateuniquefilename]] `EXTRACTED`
- [[fileuploadservice_fileuploadservice_validatemimetype]] —calls→ [[fileuploadservice_fileuploadservice_getallowedmimes]] `EXTRACTED`
