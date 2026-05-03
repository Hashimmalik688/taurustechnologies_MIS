# Community 31
**11 nodes**

## Members
- [[app_imports_leadsimport_php]]
- [[app_support_importsanitizer_php]]
- [[importsanitizer_importsanitizer]]
- [[importsanitizer_importsanitizer_parseexceldate]]
- [[importsanitizer_importsanitizer_parsemoney]]
- [[leadsimport_leadsimport]]
- [[leadsimport_leadsimport_collection]]
- [[leadsimport_leadsimport_getvaluefromrow]]
- [[leadsimport_leadsimport_normalizephonenumber]]
- [[leadsimport_leadsimport_parseexceldate]]
- [[leadsimport_leadsimport_parsemoney]]

## Internal connections
- [[app_imports_leadsimport_php]] —contains→ [[leadsimport_leadsimport]] `EXTRACTED`
- [[leadsimport_leadsimport]] —method→ [[leadsimport_leadsimport_collection]] `EXTRACTED`
- [[leadsimport_leadsimport]] —method→ [[leadsimport_leadsimport_parseexceldate]] `EXTRACTED`
- [[leadsimport_leadsimport]] —method→ [[leadsimport_leadsimport_parsemoney]] `EXTRACTED`
- [[leadsimport_leadsimport]] —method→ [[leadsimport_leadsimport_normalizephonenumber]] `EXTRACTED`
- [[leadsimport_leadsimport]] —method→ [[leadsimport_leadsimport_getvaluefromrow]] `EXTRACTED`
- [[leadsimport_leadsimport_collection]] —calls→ [[leadsimport_leadsimport_getvaluefromrow]] `EXTRACTED`
- [[leadsimport_leadsimport_collection]] —calls→ [[leadsimport_leadsimport_normalizephonenumber]] `EXTRACTED`
- [[leadsimport_leadsimport_collection]] —calls→ [[importsanitizer_importsanitizer]] `INFERRED`
- [[app_support_importsanitizer_php]] —contains→ [[importsanitizer_importsanitizer]] `EXTRACTED`
- [[importsanitizer_importsanitizer]] —method→ [[importsanitizer_importsanitizer_parseexceldate]] `EXTRACTED`
- [[importsanitizer_importsanitizer]] —method→ [[importsanitizer_importsanitizer_parsemoney]] `EXTRACTED`
