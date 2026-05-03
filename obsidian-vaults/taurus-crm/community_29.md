# Community 29
**8 nodes**

## Members
- [[app_models_insurancecarrier_php]]
- [[insurancecarrier_insurancecarrier]]
- [[insurancecarrier_insurancecarrier_agentcommissions]]
- [[insurancecarrier_insurancecarrier_agentstates]]
- [[insurancecarrier_insurancecarrier_commissionbrackets]]
- [[insurancecarrier_insurancecarrier_getcommissionforage]]
- [[insurancecarrier_insurancecarrier_getcommissionforagent]]
- [[insurancecarrier_insurancecarrier_leads]]

## Internal connections
- [[app_models_insurancecarrier_php]] —contains→ [[insurancecarrier_insurancecarrier]] `EXTRACTED`
- [[insurancecarrier_insurancecarrier]] —method→ [[insurancecarrier_insurancecarrier_leads]] `EXTRACTED`
- [[insurancecarrier_insurancecarrier]] —method→ [[insurancecarrier_insurancecarrier_commissionbrackets]] `EXTRACTED`
- [[insurancecarrier_insurancecarrier]] —method→ [[insurancecarrier_insurancecarrier_getcommissionforage]] `EXTRACTED`
- [[insurancecarrier_insurancecarrier]] —method→ [[insurancecarrier_insurancecarrier_agentcommissions]] `EXTRACTED`
- [[insurancecarrier_insurancecarrier]] —method→ [[insurancecarrier_insurancecarrier_agentstates]] `EXTRACTED`
- [[insurancecarrier_insurancecarrier]] —method→ [[insurancecarrier_insurancecarrier_getcommissionforagent]] `EXTRACTED`
- [[insurancecarrier_insurancecarrier_commissionbrackets]] —calls→ [[insurancecarrier_insurancecarrier_getcommissionforage]] `EXTRACTED`
- [[insurancecarrier_insurancecarrier_agentcommissions]] —calls→ [[insurancecarrier_insurancecarrier_getcommissionforagent]] `EXTRACTED`
