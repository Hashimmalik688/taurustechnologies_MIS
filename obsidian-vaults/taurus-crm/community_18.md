# Community 18
**19 nodes**

## Members
- [[announcement_announcement]]
- [[announcement_announcement_createdby]]
- [[announcement_announcement_getanimationclass]]
- [[announcement_announcement_getbackgroundclass]]
- [[announcement_announcement_getcurrent]]
- [[announcement_announcement_geticonclass]]
- [[announcement_announcement_scopeactive]]
- [[announcementcontroller_announcementcontroller]]
- [[announcementcontroller_announcementcontroller_destroy]]
- [[announcementcontroller_announcementcontroller_getcurrent]]
- [[announcementcontroller_announcementcontroller_index]]
- [[announcementcontroller_announcementcontroller_store]]
- [[announcementcontroller_announcementcontroller_toggle]]
- [[announcementcontroller_announcementcontroller_update]]
- [[announcementseeder_announcementseeder]]
- [[announcementseeder_announcementseeder_run]]
- [[app_http_controllers_admin_announcementcontroller_php]]
- [[app_models_announcement_php]]
- [[database_seeders_announcementseeder_php]]

## Internal connections
- [[app_models_announcement_php]] —contains→ [[announcement_announcement]] `EXTRACTED`
- [[announcement_announcement]] —method→ [[announcement_announcement_createdby]] `EXTRACTED`
- [[announcement_announcement]] —method→ [[announcement_announcement_scopeactive]] `EXTRACTED`
- [[announcement_announcement]] —method→ [[announcement_announcement_getcurrent]] `EXTRACTED`
- [[announcement_announcement]] —method→ [[announcement_announcement_getanimationclass]] `EXTRACTED`
- [[announcement_announcement]] —method→ [[announcement_announcement_getbackgroundclass]] `EXTRACTED`
- [[announcement_announcement]] —method→ [[announcement_announcement_geticonclass]] `EXTRACTED`
- [[announcement_announcement]] —calls→ [[announcementcontroller_announcementcontroller_index]] `INFERRED`
- [[announcement_announcement]] —calls→ [[announcementcontroller_announcementcontroller_store]] `INFERRED`
- [[announcement_announcement]] —calls→ [[announcementcontroller_announcementcontroller_update]] `INFERRED`
- [[announcement_announcement]] —calls→ [[announcementcontroller_announcementcontroller_toggle]] `INFERRED`
- [[announcement_announcement]] —calls→ [[announcementcontroller_announcementcontroller_getcurrent]] `INFERRED`
- [[announcement_announcement]] —calls→ [[announcementseeder_announcementseeder_run]] `INFERRED`
- [[announcement_announcement_getanimationclass]] —calls→ [[announcementcontroller_announcementcontroller_getcurrent]] `INFERRED`
- [[announcement_announcement_getbackgroundclass]] —calls→ [[announcementcontroller_announcementcontroller_getcurrent]] `INFERRED`
- [[announcement_announcement_geticonclass]] —calls→ [[announcementcontroller_announcementcontroller_getcurrent]] `INFERRED`
- [[app_http_controllers_admin_announcementcontroller_php]] —contains→ [[announcementcontroller_announcementcontroller]] `EXTRACTED`
- [[announcementcontroller_announcementcontroller]] —method→ [[announcementcontroller_announcementcontroller_index]] `EXTRACTED`
- [[announcementcontroller_announcementcontroller]] —method→ [[announcementcontroller_announcementcontroller_store]] `EXTRACTED`
- [[announcementcontroller_announcementcontroller]] —method→ [[announcementcontroller_announcementcontroller_update]] `EXTRACTED`
- [[announcementcontroller_announcementcontroller]] —method→ [[announcementcontroller_announcementcontroller_toggle]] `EXTRACTED`
- [[announcementcontroller_announcementcontroller]] —method→ [[announcementcontroller_announcementcontroller_destroy]] `EXTRACTED`
- [[announcementcontroller_announcementcontroller]] —method→ [[announcementcontroller_announcementcontroller_getcurrent]] `EXTRACTED`
- [[announcementcontroller_announcementcontroller_store]] —calls→ [[announcementcontroller_announcementcontroller_update]] `EXTRACTED`
- [[announcementcontroller_announcementcontroller_update]] —calls→ [[announcementcontroller_announcementcontroller_toggle]] `EXTRACTED`
- [[database_seeders_announcementseeder_php]] —contains→ [[announcementseeder_announcementseeder]] `EXTRACTED`
- [[announcementseeder_announcementseeder]] —method→ [[announcementseeder_announcementseeder_run]] `EXTRACTED`
