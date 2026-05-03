# Community 26
**15 nodes**

## Members
- [[app_http_controllers_partner_partnerauthcontroller_php]]
- [[app_http_middleware_partnerauthenticate_php]]
- [[app_http_middleware_preventpartneraccess_php]]
- [[app_http_middleware_preventuseraccess_php]]
- [[partnerauthcontroller_partnerauthcontroller]]
- [[partnerauthcontroller_partnerauthcontroller_login]]
- [[partnerauthcontroller_partnerauthcontroller_logout]]
- [[partnerauthcontroller_partnerauthcontroller_showloginform]]
- [[partnerauthenticate_partnerauthenticate]]
- [[partnerauthenticate_partnerauthenticate_handle]]
- [[partneruserseparationtest_partneruserseparationtest_user_cannot_access_partner_routes]]
- [[preventpartneraccess_preventpartneraccess]]
- [[preventpartneraccess_preventpartneraccess_handle]]
- [[preventuseraccess_preventuseraccess]]
- [[preventuseraccess_preventuseraccess_handle]]

## Internal connections
- [[app_http_middleware_preventpartneraccess_php]] —contains→ [[preventpartneraccess_preventpartneraccess]] `EXTRACTED`
- [[preventpartneraccess_preventpartneraccess]] —method→ [[preventpartneraccess_preventpartneraccess_handle]] `EXTRACTED`
- [[preventpartneraccess_preventpartneraccess_handle]] —calls→ [[partnerauthcontroller_partnerauthcontroller_logout]] `INFERRED`
- [[app_http_middleware_partnerauthenticate_php]] —contains→ [[partnerauthenticate_partnerauthenticate]] `EXTRACTED`
- [[partnerauthenticate_partnerauthenticate]] —method→ [[partnerauthenticate_partnerauthenticate_handle]] `EXTRACTED`
- [[partnerauthenticate_partnerauthenticate_handle]] —calls→ [[partnerauthcontroller_partnerauthcontroller_logout]] `INFERRED`
- [[app_http_middleware_preventuseraccess_php]] —contains→ [[preventuseraccess_preventuseraccess]] `EXTRACTED`
- [[preventuseraccess_preventuseraccess]] —method→ [[preventuseraccess_preventuseraccess_handle]] `EXTRACTED`
- [[preventuseraccess_preventuseraccess_handle]] —calls→ [[partnerauthcontroller_partnerauthcontroller_logout]] `INFERRED`
- [[app_http_controllers_partner_partnerauthcontroller_php]] —contains→ [[partnerauthcontroller_partnerauthcontroller]] `EXTRACTED`
- [[partnerauthcontroller_partnerauthcontroller]] —method→ [[partnerauthcontroller_partnerauthcontroller_showloginform]] `EXTRACTED`
- [[partnerauthcontroller_partnerauthcontroller]] —method→ [[partnerauthcontroller_partnerauthcontroller_login]] `EXTRACTED`
- [[partnerauthcontroller_partnerauthcontroller]] —method→ [[partnerauthcontroller_partnerauthcontroller_logout]] `EXTRACTED`
- [[partnerauthcontroller_partnerauthcontroller_login]] —calls→ [[partnerauthcontroller_partnerauthcontroller_logout]] `EXTRACTED`
- [[partnerauthcontroller_partnerauthcontroller_login]] —calls→ [[partneruserseparationtest_partneruserseparationtest_user_cannot_access_partner_routes]] `INFERRED`
