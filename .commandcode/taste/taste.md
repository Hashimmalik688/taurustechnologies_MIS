# Taste (Continuously Learned by [CommandCode][cmd])

[cmd]: https://commandcode.ai/

# Workflow
- When asked to fix or copy data, offer a permanent UI/feature solution alongside any one-off manual fix — the user builds tools that need to work for others long-term. Confidence: 0.75

# Architecture
- Downline agents are full partners with all the same fields, organized purely by parent_partner_id hierarchy — do NOT use a type discriminator column to separate partners from agents. The hierarchy is organizational, not structural. Confidence: 0.80

# Peregrines
- Sales should be counted on the date the Peregrine closer originally sent them to the validator, NOT the date the validator marked them as sale. The validator's submission timestamp should be recorded separately but not override the original sale date. Confidence: 0.80

