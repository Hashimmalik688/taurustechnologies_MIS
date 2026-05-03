# Community 17
**24 nodes**

## Members
- [[app_http_controllers_admin_ticketcontroller_php]]
- [[app_models_pabsticket_php]]
- [[app_models_pabsticketcomment_php]]
- [[pabsticket_pabsticket]]
- [[pabsticket_pabsticket_assignee]]
- [[pabsticket_pabsticket_comments]]
- [[pabsticket_pabsticket_creator]]
- [[pabsticket_pabsticket_project]]
- [[pabsticket_pabsticket_scopebysection]]
- [[pabsticket_pabsticket_scopebystatus]]
- [[pabsticket_pabsticket_scopeclosed]]
- [[pabsticket_pabsticket_scopeopen]]
- [[pabsticketcomment_pabsticketcomment]]
- [[pabsticketcomment_pabsticketcomment_ticket]]
- [[pabsticketcomment_pabsticketcomment_user]]
- [[ticketcontroller_ticketcontroller]]
- [[ticketcontroller_ticketcontroller_addcomment]]
- [[ticketcontroller_ticketcontroller_approve]]
- [[ticketcontroller_ticketcontroller_close]]
- [[ticketcontroller_ticketcontroller_generateticketcode]]
- [[ticketcontroller_ticketcontroller_reject]]
- [[ticketcontroller_ticketcontroller_resolve]]
- [[ticketcontroller_ticketcontroller_store]]
- [[ticketcontroller_ticketcontroller_update]]

## Internal connections
- [[app_models_pabsticketcomment_php]] —contains→ [[pabsticketcomment_pabsticketcomment]] `EXTRACTED`
- [[pabsticketcomment_pabsticketcomment]] —method→ [[pabsticketcomment_pabsticketcomment_ticket]] `EXTRACTED`
- [[pabsticketcomment_pabsticketcomment]] —method→ [[pabsticketcomment_pabsticketcomment_user]] `EXTRACTED`
- [[pabsticketcomment_pabsticketcomment]] —calls→ [[ticketcontroller_ticketcontroller_addcomment]] `INFERRED`
- [[pabsticketcomment_pabsticketcomment]] —calls→ [[ticketcontroller_ticketcontroller_approve]] `INFERRED`
- [[pabsticketcomment_pabsticketcomment]] —calls→ [[ticketcontroller_ticketcontroller_reject]] `INFERRED`
- [[app_models_pabsticket_php]] —contains→ [[pabsticket_pabsticket]] `EXTRACTED`
- [[pabsticket_pabsticket]] —method→ [[pabsticket_pabsticket_project]] `EXTRACTED`
- [[pabsticket_pabsticket]] —method→ [[pabsticket_pabsticket_creator]] `EXTRACTED`
- [[pabsticket_pabsticket]] —method→ [[pabsticket_pabsticket_assignee]] `EXTRACTED`
- [[pabsticket_pabsticket]] —method→ [[pabsticket_pabsticket_comments]] `EXTRACTED`
- [[pabsticket_pabsticket]] —method→ [[pabsticket_pabsticket_scopebysection]] `EXTRACTED`
- [[pabsticket_pabsticket]] —method→ [[pabsticket_pabsticket_scopebystatus]] `EXTRACTED`
- [[pabsticket_pabsticket]] —method→ [[pabsticket_pabsticket_scopeopen]] `EXTRACTED`
- [[pabsticket_pabsticket]] —method→ [[pabsticket_pabsticket_scopeclosed]] `EXTRACTED`
- [[pabsticket_pabsticket]] —calls→ [[ticketcontroller_ticketcontroller_store]] `INFERRED`
- [[pabsticket_pabsticket]] —calls→ [[ticketcontroller_ticketcontroller_generateticketcode]] `INFERRED`
- [[app_http_controllers_admin_ticketcontroller_php]] —contains→ [[ticketcontroller_ticketcontroller]] `EXTRACTED`
- [[ticketcontroller_ticketcontroller]] —method→ [[ticketcontroller_ticketcontroller_store]] `EXTRACTED`
- [[ticketcontroller_ticketcontroller]] —method→ [[ticketcontroller_ticketcontroller_update]] `EXTRACTED`
- [[ticketcontroller_ticketcontroller]] —method→ [[ticketcontroller_ticketcontroller_addcomment]] `EXTRACTED`
- [[ticketcontroller_ticketcontroller]] —method→ [[ticketcontroller_ticketcontroller_resolve]] `EXTRACTED`
- [[ticketcontroller_ticketcontroller]] —method→ [[ticketcontroller_ticketcontroller_close]] `EXTRACTED`
- [[ticketcontroller_ticketcontroller]] —method→ [[ticketcontroller_ticketcontroller_approve]] `EXTRACTED`
- [[ticketcontroller_ticketcontroller]] —method→ [[ticketcontroller_ticketcontroller_reject]] `EXTRACTED`
- [[ticketcontroller_ticketcontroller]] —method→ [[ticketcontroller_ticketcontroller_generateticketcode]] `EXTRACTED`
- [[ticketcontroller_ticketcontroller_store]] —calls→ [[ticketcontroller_ticketcontroller_generateticketcode]] `EXTRACTED`
- [[ticketcontroller_ticketcontroller_update]] —calls→ [[ticketcontroller_ticketcontroller_resolve]] `EXTRACTED`
- [[ticketcontroller_ticketcontroller_update]] —calls→ [[ticketcontroller_ticketcontroller_close]] `EXTRACTED`
- [[ticketcontroller_ticketcontroller_update]] —calls→ [[ticketcontroller_ticketcontroller_approve]] `EXTRACTED`
