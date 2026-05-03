# Community 29
**9 nodes**

## Members
- [[accountswitchingdetector_accountswitchingdetector]]
- [[accountswitchingdetector_accountswitchingdetector_logsuspiciousactivity]]
- [[app_http_controllers_auth_logincontroller_php]]
- [[app_services_accountswitchingdetector_php]]
- [[logincontroller_logincontroller]]
- [[logincontroller_logincontroller_authenticated]]
- [[logincontroller_logincontroller_construct]]
- [[logincontroller_logincontroller_credentials]]
- [[logincontroller_logincontroller_redirectto]]

## Internal connections
- [[app_services_accountswitchingdetector_php]] —contains→ [[accountswitchingdetector_accountswitchingdetector]] `EXTRACTED`
- [[accountswitchingdetector_accountswitchingdetector]] —method→ [[accountswitchingdetector_accountswitchingdetector_logsuspiciousactivity]] `EXTRACTED`
- [[accountswitchingdetector_accountswitchingdetector]] —calls→ [[logincontroller_logincontroller_authenticated]] `INFERRED`
- [[app_http_controllers_auth_logincontroller_php]] —contains→ [[logincontroller_logincontroller]] `EXTRACTED`
- [[logincontroller_logincontroller]] —method→ [[logincontroller_logincontroller_construct]] `EXTRACTED`
- [[logincontroller_logincontroller]] —method→ [[logincontroller_logincontroller_redirectto]] `EXTRACTED`
- [[logincontroller_logincontroller]] —method→ [[logincontroller_logincontroller_credentials]] `EXTRACTED`
- [[logincontroller_logincontroller]] —method→ [[logincontroller_logincontroller_authenticated]] `EXTRACTED`
