# Community 17
**23 nodes**

## Members
- [[alloweddevice_alloweddevice]]
- [[alloweddevice_alloweddevice_addedby]]
- [[alloweddevice_alloweddevice_isapproved]]
- [[alloweddevice_alloweddevice_isdisabled]]
- [[alloweddevice_alloweddevice_ispending]]
- [[alloweddevice_alloweddevice_isrejected]]
- [[app_http_controllers_admin_devicecontroller_php]]
- [[app_http_middleware_restricttoalloweddevice_php]]
- [[app_models_alloweddevice_php]]
- [[devicecontroller_devicecontroller]]
- [[devicecontroller_devicecontroller_approve]]
- [[devicecontroller_devicecontroller_destroy]]
- [[devicecontroller_devicecontroller_disable]]
- [[devicecontroller_devicecontroller_enable]]
- [[devicecontroller_devicecontroller_index]]
- [[devicecontroller_devicecontroller_rejectallpending]]
- [[devicecontroller_devicecontroller_store]]
- [[devicecontroller_devicecontroller_update]]
- [[restricttoalloweddevice_restricttoalloweddevice]]
- [[restricttoalloweddevice_restricttoalloweddevice_disabledresponse]]
- [[restricttoalloweddevice_restricttoalloweddevice_handle]]
- [[restricttoalloweddevice_restricttoalloweddevice_makecookie]]
- [[restricttoalloweddevice_restricttoalloweddevice_pendingresponse]]

## Internal connections
- [[app_models_alloweddevice_php]] —contains→ [[alloweddevice_alloweddevice]] `EXTRACTED`
- [[alloweddevice_alloweddevice]] —method→ [[alloweddevice_alloweddevice_addedby]] `EXTRACTED`
- [[alloweddevice_alloweddevice]] —method→ [[alloweddevice_alloweddevice_isapproved]] `EXTRACTED`
- [[alloweddevice_alloweddevice]] —method→ [[alloweddevice_alloweddevice_ispending]] `EXTRACTED`
- [[alloweddevice_alloweddevice]] —method→ [[alloweddevice_alloweddevice_isdisabled]] `EXTRACTED`
- [[alloweddevice_alloweddevice]] —method→ [[alloweddevice_alloweddevice_isrejected]] `EXTRACTED`
- [[alloweddevice_alloweddevice]] —calls→ [[restricttoalloweddevice_restricttoalloweddevice_handle]] `INFERRED`
- [[alloweddevice_alloweddevice]] —calls→ [[devicecontroller_devicecontroller_store]] `INFERRED`
- [[alloweddevice_alloweddevice]] —calls→ [[devicecontroller_devicecontroller_rejectallpending]] `INFERRED`
- [[app_http_middleware_restricttoalloweddevice_php]] —contains→ [[restricttoalloweddevice_restricttoalloweddevice]] `EXTRACTED`
- [[restricttoalloweddevice_restricttoalloweddevice]] —method→ [[restricttoalloweddevice_restricttoalloweddevice_handle]] `EXTRACTED`
- [[restricttoalloweddevice_restricttoalloweddevice]] —method→ [[restricttoalloweddevice_restricttoalloweddevice_makecookie]] `EXTRACTED`
- [[restricttoalloweddevice_restricttoalloweddevice]] —method→ [[restricttoalloweddevice_restricttoalloweddevice_pendingresponse]] `EXTRACTED`
- [[restricttoalloweddevice_restricttoalloweddevice]] —method→ [[restricttoalloweddevice_restricttoalloweddevice_disabledresponse]] `EXTRACTED`
- [[restricttoalloweddevice_restricttoalloweddevice_handle]] —calls→ [[restricttoalloweddevice_restricttoalloweddevice_makecookie]] `EXTRACTED`
- [[restricttoalloweddevice_restricttoalloweddevice_handle]] —calls→ [[restricttoalloweddevice_restricttoalloweddevice_pendingresponse]] `EXTRACTED`
- [[restricttoalloweddevice_restricttoalloweddevice_handle]] —calls→ [[restricttoalloweddevice_restricttoalloweddevice_disabledresponse]] `EXTRACTED`
- [[app_http_controllers_admin_devicecontroller_php]] —contains→ [[devicecontroller_devicecontroller]] `EXTRACTED`
- [[devicecontroller_devicecontroller]] —method→ [[devicecontroller_devicecontroller_index]] `EXTRACTED`
- [[devicecontroller_devicecontroller]] —method→ [[devicecontroller_devicecontroller_store]] `EXTRACTED`
- [[devicecontroller_devicecontroller]] —method→ [[devicecontroller_devicecontroller_approve]] `EXTRACTED`
- [[devicecontroller_devicecontroller]] —method→ [[devicecontroller_devicecontroller_update]] `EXTRACTED`
- [[devicecontroller_devicecontroller]] —method→ [[devicecontroller_devicecontroller_disable]] `EXTRACTED`
- [[devicecontroller_devicecontroller]] —method→ [[devicecontroller_devicecontroller_enable]] `EXTRACTED`
- [[devicecontroller_devicecontroller]] —method→ [[devicecontroller_devicecontroller_destroy]] `EXTRACTED`
- [[devicecontroller_devicecontroller]] —method→ [[devicecontroller_devicecontroller_rejectallpending]] `EXTRACTED`
- [[devicecontroller_devicecontroller_approve]] —calls→ [[devicecontroller_devicecontroller_update]] `EXTRACTED`
- [[devicecontroller_devicecontroller_update]] —calls→ [[devicecontroller_devicecontroller_disable]] `EXTRACTED`
- [[devicecontroller_devicecontroller_update]] —calls→ [[devicecontroller_devicecontroller_enable]] `EXTRACTED`
- [[devicecontroller_devicecontroller_update]] —calls→ [[devicecontroller_devicecontroller_destroy]] `EXTRACTED`
