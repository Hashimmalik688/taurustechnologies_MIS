# Community 43
**6 nodes**

## Members
- [[app_events_callstatuschanged_php]]
- [[callstatuschanged_callstatuschanged]]
- [[callstatuschanged_callstatuschanged_broadcaston]]
- [[callstatuschanged_callstatuschanged_broadcastwith]]
- [[callstatuschanged_callstatuschanged_construct]]
- [[callstatuschanged_callstatuschanged_sanitizephonenumber]]

## Internal connections
- [[app_events_callstatuschanged_php]] —contains→ [[callstatuschanged_callstatuschanged]] `EXTRACTED`
- [[callstatuschanged_callstatuschanged]] —method→ [[callstatuschanged_callstatuschanged_construct]] `EXTRACTED`
- [[callstatuschanged_callstatuschanged]] —method→ [[callstatuschanged_callstatuschanged_broadcaston]] `EXTRACTED`
- [[callstatuschanged_callstatuschanged]] —method→ [[callstatuschanged_callstatuschanged_broadcastwith]] `EXTRACTED`
- [[callstatuschanged_callstatuschanged]] —method→ [[callstatuschanged_callstatuschanged_sanitizephonenumber]] `EXTRACTED`
- [[callstatuschanged_callstatuschanged_broadcaston]] —calls→ [[callstatuschanged_callstatuschanged_sanitizephonenumber]] `EXTRACTED`
