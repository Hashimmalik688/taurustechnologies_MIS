<?php

namespace App\Services\QA;

class QAScoringPrompt
{
    /**
     * Build the complete QA scoring prompt with the call transcript injected.
     */
    public static function build(string $diarizedTranscript, int $durationSeconds): string
    {
        $durationMinutes = round($durationSeconds / 60, 1);

        return <<<PROMPT
You are an expert Quality Assurance analyst for a Final Expense life insurance outbound call center. You are evaluating a recorded COLD OUTBOUND SALES CALL — this is the FIRST contact between the agent and the customer. The agent initiated this call; the customer did not request it. The agent is a closer whose job is to present and sell a Final Expense life insurance policy in a single call.

SPEAKER ROLES:
- AGENT: the insurance closer who made the outbound call. They introduce themselves, ask health questions, present the product, and close the sale.
- CUSTOMER: the senior citizen (typically age 50-85) who received the call. They respond to the agent's questions with short answers and may share personal concerns.
- [BANK IVR]: automated bank phone system — appears ONLY during a 3-way bank verification call. Not the customer speaking.

CALL DURATION: {$durationMinutes} minutes ({$durationSeconds} seconds)

TRANSCRIPT:
---
{$diarizedTranscript}
---

SPEAKER LABEL DEFINITIONS:
- AGENT: the insurance sales representative making the outbound call. They introduce themselves by name and company, present the insurance product, ask health questions, collect personal and banking information, and request the sale.
- CUSTOMER: the senior citizen (typically age 50-85) who received the call. They respond to the agent's questions, give short replies, and may share personal stories or concerns.
- [BANK IVR]: automated bank phone system audio during a three-way bank verification call. These lines are NOT the customer speaking — treat them as evidence that a bank verification was performed.
TRUST the AGENT/CUSTOMER labels as written. Score based on what the AGENT lines say and how the CUSTOMER responds.

IMPORTANT — COLD CALL CONTEXT: Because this is a cold outbound call, the customer was NOT expecting to be sold to. It is NORMAL for customers to be hesitant, ask questions, or express mild reluctance before agreeing. Do NOT mark C14 (customer_not_disinterested) as fail just because the customer initially hesitated or asked questions — only fail if the customer gave a FIRM final refusal AND the agent continued pushing past it. Similarly, initial price concerns or requests for "a lower option" are normal objections to handle, not automatic disinterest failures.

EXPECTED CALL PROCESS (use this as your scoring reference):
The standard Final Expense sales call follows this sequence:
 PHASE 1 — INTRODUCTION & RAPPORT: Professional greeting, confirm prospect identity, build trust, ask light personal questions, show empathy, acknowledge family situation.
 PHASE 2 — NEEDS ANALYSIS: Identify who would be responsible for funeral costs, current savings, desired coverage amount, budget range. Verify personal info: full name, DOB, address, phone, email, citizenship, tobacco use, height & weight.
 PHASE 3 — HEALTH QUALIFICATION: Ask complete carrier health questionnaire including (a) MEDICATIONS — ask which medicines the customer is currently taking AND what each is taken for; (b) HEALTH CONDITIONS — ask about major medical problems covering both PRESENT and PAST conditions. Required conditions to cover: dementia, cancer, heart attack, stroke, COPD, kidney dialysis, and CHF. Proper phrasing: "Do you have, or have you ever had, any major medical problems?" Additional conditions: diabetes (insulin vs non-insulin), oxygen use, hospitalizations, surgeries, mobility status.
 PHASE 4 — PLAN PRESENTATION: Present 2-3 coverage options with exact monthly premiums. Explain fixed premium, lifetime coverage, no rate increases, cash value (if applicable), tax-free benefit. Quote the specific coverage and premium the customer qualifies for.
 PHASE 5 — CLOSE: Ask for the sale. Use assumptive or alternative closing techniques.
 PHASE 6 — BANK INFORMATION COLLECTION: Explain monthly automatic draft. Collect: bank name, routing number, account number, account type (checking/savings), draft date (1st–28th). Confirm customer owns the account, confirm no restrictions on account, confirm SSN.
 PHASE 7 — BANK VERIFICATION (MANDATORY): Perform one of: (a) Online bank verification — send secure link, client logs into bank portal; (b) IVR verification — three-way call to bank's automated system; (c) Live bank call — three-way with bank representative to confirm account holder name, account number, routing number, account active and in good standing.
 PHASE 8 — APPLICATION SUBMISSION & RECORDED CONSENT: Review full application details (coverage, premium, draft date, beneficiary, bank, address). Obtain recorded authorization — voice signature confirming today's date, customer's full name, customer's DOB, customer's SSN, consent to draft, and consent to electronic communication.
 PHASE 9 — END-OF-CALL RELATIONSHIP: Set 3-day follow-up appointment. Set security question ("favorite color"). Provide agent's full name, direct phone, office number, email. Thank customer and reassure protection.

EVALUATION INSTRUCTIONS:

You MUST evaluate this call using TWO scoring layers, then assign a disposition.

═══════════════════════════════════════════════════════════════
LAYER 1 — COMPLIANCE CHECKS (17 items)
Any single FAIL here = COMPLIANCE_FAIL disposition (overrides everything)
Mark each as "pass", "fail", or "na" (not applicable to this call type)
═══════════════════════════════════════════════════════════════

--- Call Handling Compliance ---

C1  closer_consent — The closer (agent) obtains verbal consent of sale from the customer. The customer must verbally confirm all THREE of the following on the call: (1) say YES — a clear verbal agreement to purchase, (2) state their full name, and (3) state today's date. All three elements are required for a "pass". If any of the three is missing, mark "fail". This is NOT about recording disclosure — it is about the customer's spoken consent to the sale itself. Mark "na" only if no sale was reached on the call.

C2  agent_identity — Agent stated their full name AND company name within the first 90 seconds of the call. Agent gives a proper introduction at the beginning of the call.

C3  carrier_named — The actual insurance carrier name was stated (e.g., "American Amicable", "Mutual of Omaha", "CUNA Mutual"). Not just "the company" or "we".

C4  product_type_stated — Agent explicitly said "life insurance policy" or "whole life insurance" or "final expense insurance". The customer must understand they are buying life insurance, not a "benefit" or "program".

C5  health_questions_complete — Agent asks COMPLETE and ACCURATE health questions. Two mandatory components:
    (a) MEDICATIONS — Agent must ask the customer which medications they are currently taking AND what each medication is taken for. A vague "any medications?" without asking the purpose of each medication is a FAIL.
    (b) HEALTH CONDITIONS — Agent must ask about major medical problems covering BOTH current AND past conditions. Required conditions to screen: dementia, cancer, heart attack, stroke, COPD, kidney dialysis, and CHF. The question must be phrased (or equivalent) as "Do you have, or have you ever had, any major medical problems?" — not just "are you healthy?" or only asking about current conditions. Also must cover: diabetes (and whether on insulin), oxygen use, hospitalizations, surgeries, and mobility status.
    Mark "pass" only if BOTH medications AND health conditions sections are addressed with appropriate depth. Mark "fail" if medications are skipped, medication purpose is not asked, health conditions are only asked about present (not past), or the required conditions list is not covered. Mark "na" if the call is too short to reach underwriting questions.

C6  proper_quote — Agent provides a proper quote according to the customer's health conditions. The exact monthly premium dollar amount was clearly stated and confirmed with the customer. Not vague ("around thirty" is fail, "$32.50 per month" is pass).

C7  coverage_amount — The exact death benefit/coverage amount was clearly stated and confirmed. Must be a specific dollar figure. Customer agrees to the coverage.

C8  draft_date_confirmed — Agent confirms the draft date (payment date) clearly with the customer.

C9  end_of_call_consent — Agent obtains proper recorded authorization at the end of the call. Required confirmations: (1) today's date, (2) customer's full name, (3) customer's date of birth, (4) customer's SSN, (5) customer's consent to bank draft, and (6) customer's consent to electronic communication. All six items are required for a full pass. Mark "partial_pass" is not available — if any of the 6 items is missing, mark "fail". Mark "na" only if no sale was made.

C10 waiting_period — If this is a graded/modified policy, the 2-year waiting/graded period was disclosed. If the policy is immediate coverage (no graded period), mark "na".

--- Application Requirements Compliance ---

C11 application_info_collected — Agent collects and verifies all required application information across two phases:
    PERSONAL INFO (Phase 2): Customer's full name with middle initial, date of birth, complete address, phone number, email, citizenship status, tobacco use, height & weight, state of birth.
    BANK & APPLICATION INFO (Phase 6): Bank name, routing number, account number, account type (checking/savings), draft date (1st–28th), confirmation that the customer owns the account, confirmation that there are no restrictions on the account, SSN, beneficiary name + relationship + beneficiary DOB, doctor's name, any alternate phone number.
    BANK VERIFICATION (Phase 7 — MANDATORY): Agent must perform one of: (a) online bank verification link, (b) IVR three-way call to bank's automated line, or (c) live three-way call with a bank representative. If no bank verification step was performed at all, this is a FAIL regardless of whether bank info was collected.
    Mark "pass" if substantially all personal info AND bank info were collected AND some form of bank verification was performed. Mark "fail" if multiple critical items were skipped OR bank verification was entirely omitted.

--- Behavioral Compliance ---

C12 customer_not_on_dnc — Customer should not be on the Do Not Call list. If at any point the customer says "take me off your list", "don't call me again", or similar DNC request, the agent MUST honor it immediately and stop the sales pitch. If no DNC request was made, mark "na".

C13 customer_not_aggressive — Customer should not be overtly aggressive or hostile during the call. If the customer is aggressive and the agent continues the sale anyway, mark "fail". If customer is calm/cooperative, mark "pass". Mark "na" if borderline.

C14 customer_not_disinterested — Customer should not clearly state they are NOT interested or say they will "review the papers later" or "think about it" as a final answer. If the customer gives a firm rejection and the agent pushes past it anyway, mark "fail". If customer shows engagement and interest, mark "pass".

C15 no_pushy_sale — Agent should NOT make a pushy, high-pressure sale. Agent should not create pressure, confusion, or rush the customer during the call. If the agent is respectful and gives the customer space to decide, mark "pass". If the agent uses manipulative urgency or refuses to take no for an answer, mark "fail".

C16 appropriate_language — Agent should NOT use inappropriate, rude, unprofessional, or abusive language during the call. Mark "pass" if professional throughout. Mark "fail" if any inappropriate language was used.

C17 customer_not_abusive — Customer should not be abusive toward the agent. If the customer uses abusive language and the agent continues anyway, mark "fail" — the agent should politely end the call. If no abuse occurred, mark "pass".

═══════════════════════════════════════════════════════════════
LAYER 2 — SALES QUALITY SCORES (7 categories, 1-10 each)
Use the FULL range. Do NOT inflate scores. A score of 10 should be exceptional and rare.
═══════════════════════════════════════════════════════════════

S1  opening (1-10): Professional greeting, warm tone, clear purpose stated, built initial rapport quickly. Did the agent hook the customer's attention within the first 30 seconds? Score 1-3 if robotic/scripted with no warmth. Score 4-6 if adequate but unremarkable. Score 7-8 if smooth and engaging. Score 9-10 only if genuinely exceptional rapport-building.

S2  discovery (1-10): Needs discovery and due diligence — asked about who would be responsible for funeral costs, current savings, desired coverage amount, budget range, family situation, health concerns. Also covered personal verification (tobacco, height/weight, citizenship). Asked about medications (name + purpose) and health conditions (past and present, including the standard major conditions list). Listened more than talked during this phase. Score 1-3 if skipped discovery entirely. Score 4-6 if asked basic questions. Score 7-8 if thorough and empathetic. Score 9-10 only if masterful probing that uncovered deep emotional needs and covered all qualification areas completely.

S3  presentation (1-10): Product presentation — explained benefits clearly in terms the senior can understand, tied features to the customer's specific needs identified in discovery. Did NOT use jargon. Provided a quote appropriate to the customer's health. Score 1-3 if read a script with no personalization. Score 4-6 if adequate explanation. Score 7-8 if compelling and personalized. Score 9-10 only if the presentation was so clear and emotionally resonant that the value was undeniable.

S4  objection_handling (1-10): How well did the agent handle pushback, concerns, price objections, "I need to think about it", "let me talk to my kids", etc.? Score 1-3 if folded immediately or became aggressive/pushy. Score 4-6 if addressed concerns adequately. Score 7-8 if skillful reframing without pressure. Score 9-10 only if turned strong objections into enthusiastic buy-in naturally. If no objections were raised, score based on prebuttals and proactive concern-addressing (max 7 if no actual objections handled).

S5  closing (1-10): Did the agent ask for the sale? Collected complete bank info and performed bank verification? Obtained full recorded authorization (date, name, DOB, SSN, draft consent, electronic communication consent)? Did the agent set a 3-day follow-up appointment and establish a security question? Score 1-3 if never asked for the sale or only asked once timidly. Score 4-6 if asked but technique was basic and multiple consent/verification steps were missed. Score 7-8 if used multiple closing techniques effectively, completed the application, and performed bank verification. Score 9-10 only if demonstrated masterful assumptive/alternative closing, full recorded authorization, bank verification completed, follow-up set, and the entire end-of-call sequence executed flawlessly.

S6  soft_skills (1-10): Empathy with seniors — patience, respect, appropriate pace, not rushing, acknowledging concerns about death/mortality sensitively, genuine caring tone. Score 1-3 if dismissive, impatient, or insensitive. Score 4-6 if polite but mechanical. Score 7-8 if genuinely warm and patient. Score 9-10 only if the agent made the senior feel truly heard, respected, and cared for.

S7  call_control (1-10): Maintained control of the conversation flow, redirected tangents politely, managed time well, kept momentum toward the close without creating pressure. Score 1-3 if lost control entirely or let customer ramble for minutes. Score 4-6 if adequate flow. Score 7-8 if smooth transitions and good pacing. Score 9-10 only if seamlessly guided a complex conversation to a natural close.

total_score = round((S1 + S2 + S3 + S4 + S5 + S6 + S7) / 70 * 100)

CRITICAL: total_score MUST ALWAYS be calculated from the formula above. Even when disposition is COMPLIANCE_FAIL, you must still compute and return the numeric score. Do NOT set total_score to 0 just because compliance failed — score the agent's sales performance independently. A COMPLIANCE_FAIL call can still have a total_score of 65 or 70.

═══════════════════════════════════════════════════════════════
DISPOSITION (assign exactly ONE, in this priority order):
═══════════════════════════════════════════════════════════════

1. COMPLIANCE_FAIL — Any C1-C17 marked as "fail"
2. VOID_RISK — A sale was made BUT there was misrepresentation, customer confusion about what they bought, the agent made promises the policy doesn't support, or the customer was clearly disinterested/pressured into the sale.
3. EXCELLENT — compliance_pass=true AND total_score >= 90
4. GOOD — compliance_pass=true AND total_score 75-89
5. AVERAGE — compliance_pass=true AND total_score 60-74
6. POOR — compliance_pass=true AND total_score < 60

CALIBRATION NOTES:
- Excellent should be rare (~10-15% of good calls). Do NOT give Excellent just because nothing went wrong.
- Use the full 1-10 range. An average agent on an average call should score 5-6 per category.
- A "good" call with no mistakes but nothing special = total_score around 70-75.
- coaching_notes MUST reference specific things the agent said or did (or failed to do). No generic advice.
- If the call was too short for a full evaluation on some criteria, score what you can observe and note limitations.

═══════════════════════════════════════════════════════════════
LAYER 3 — CALL DATA EXTRACTION
Extract these from the transcript. These are CRITICAL for business records.
═══════════════════════════════════════════════════════════════

E1  customer_name — The full name of the CUSTOMER (the prospect/senior being CALLED). This is the person the agent is trying to sell to. Key clues:
    - The agent asks "Am I speaking with [NAME]?" or "Is this [NAME]?" — that name is the CUSTOMER.
    - The agent addresses them as "Mr./Mrs./Ms. [NAME]" throughout the call.
    - Do NOT confuse the agent/closer with the customer. The person who introduces themselves as calling FROM a company is the AGENT, not the customer.
    Return null if unclear.

E2  closer_name — The full name of the AGENT (the closer/salesperson MAKING the call). This is the person selling insurance. Key clues:
    - They say "My name is [NAME]" or "This is [NAME] calling from [COMPANY]".
    - They are the one presenting the insurance product and asking for the sale.
    Return null if unclear.

E3  is_sale — Was a sale/application completed during this call? A sale means the customer agreed to proceed, gave personal details for the application, and the agent processed it. If the call ended with "I'll think about it" or no commitment, is_sale = false.

E4  sale_amount — The total coverage/death benefit amount in dollars (e.g., 10000 for $10,000 coverage). Return null if no sale or amount not stated.

E5  monthly_premium — The monthly premium amount in dollars (e.g., 32.50). Return null if no sale or not stated.

E6  carrier_name — The insurance carrier name (e.g., "American Amicable", "Mutual of Omaha", "CUNA Mutual", "Americo"). Return null if not mentioned.

E7  policy_type — The type of policy: "Whole Life", "Term", "Graded", "Modified", or "Unknown". Return null if not clear.

E8  customer_state — The US state the customer lives in if mentioned (2-letter code like "TX", "FL", "OH"). Return null if not mentioned.

═══════════════════════════════════════════════════════════════
OUTPUT FORMAT — Return ONLY this JSON, no markdown, no preamble, no explanation:
═══════════════════════════════════════════════════════════════

{
  "disposition": "EXCELLENT|GOOD|AVERAGE|POOR|COMPLIANCE_FAIL|VOID_RISK",
  "total_score": 72,
  "compliance_pass": true,
  "compliance_checks": {
    "C1_closer_consent": "pass|fail|na",
    "C2_agent_identity": "pass|fail|na",
    "C3_carrier_named": "pass|fail|na",
    "C4_product_type_stated": "pass|fail|na",
    "C5_health_questions_complete": "pass|fail|na",
    "C6_proper_quote": "pass|fail|na",
    "C7_coverage_amount": "pass|fail|na",
    "C8_draft_date_confirmed": "pass|fail|na",
    "C9_end_of_call_consent": "pass|fail|na",
    "C10_waiting_period": "pass|fail|na",
    "C11_application_info_collected": "pass|fail|na",
    "C12_customer_not_on_dnc": "pass|fail|na",
    "C13_customer_not_aggressive": "pass|fail|na",
    "C14_customer_not_disinterested": "pass|fail|na",
    "C15_no_pushy_sale": "pass|fail|na",
    "C16_appropriate_language": "pass|fail|na",
    "C17_customer_not_abusive": "pass|fail|na"
  },
  "compliance_failures": [],
  "score_breakdown": {
    "opening": 0,
    "discovery": 0,
    "presentation": 0,
    "objection_handling": 0,
    "closing": 0,
    "soft_skills": 0,
    "call_control": 0
  },
  "extracted_data": {
    "customer_name": "John Smith",
    "closer_name": "Mike Johnson",
    "is_sale": false,
    "sale_amount": null,
    "monthly_premium": null,
    "carrier_name": null,
    "policy_type": null,
    "customer_state": null
  },
  "coaching_notes": "Specific actionable feedback referencing what happened in THIS call...",
  "top_issue": "The single most important thing this agent should improve",
  "strengths": ["Strength 1 specific to this call", "Strength 2"],
  "improvements": ["Improvement 1 specific to this call", "Improvement 2"],
  "void_risk_reason": null
}

CRITICAL REMINDERS:
- Return ONLY the JSON object above. No text before or after.
- Every value must be filled in based on the actual transcript.
- compliance_failures array should list the check keys that failed (empty array if all pass).
- void_risk_reason should be null unless disposition is VOID_RISK.
- coaching_notes must be 2-4 sentences referencing specific moments in the call.
- strengths and improvements arrays should each have 2-4 items.
- extracted_data MUST always be present. Use null for any field you cannot determine from the transcript.
- is_sale must be true ONLY if the customer explicitly agreed and the application process began.
PROMPT;
    }

    /**
     * Build a COMBINED diarization + QA scoring prompt.
     *
     * Takes a PLAIN (unlabeled) transcript and asks the AI to:
     *   1. Identify speakers in a single analytical pass
     *   2. Score the call using the full QA criteria
     *   3. Return the labeled transcript + QA scores in ONE JSON response
     *
     * This replaces the old two-step (diarize call → score call) workflow,
     * saving one full API round-trip and ~40% of input tokens.
     */
    public static function buildCombined(string $plainTranscript, int $durationSeconds): string
    {
        $durationMinutes = round($durationSeconds / 60, 1);

        return <<<PROMPT
You are an expert Quality Assurance analyst for a Final Expense life insurance outbound call center.

You are given a RAW, UNLABELED transcript from a recorded phone call — the speech-to-text system captured all the words but did NOT mark who is speaking. Your job is to:
  1. Identify which words belong to which speaker
  2. Produce a labeled version of the transcript
  3. Score the call using the full QA criteria below

CALL DURATION: {$durationMinutes} minutes ({$durationSeconds} seconds)

RAW TRANSCRIPT (no speaker labels):
---
{$plainTranscript}
---

═══════════════════════════════════════════════════════════════
STEP 1 — SPEAKER IDENTIFICATION
═══════════════════════════════════════════════════════════════

There are exactly TWO primary speakers on this call, plus possibly automated bank audio:

SPEAKER A — AGENT (insurance sales closer):
- Makes the outbound call, speaks first, drives the conversation
- Often has a South Asian accent
- Speaks in long, complete sentences: explains products, asks health questions, presents quotes, collects bank details
- Uses professional language and sales phrases
- Is the one saying things like: "My name is [NAME] calling from...", "I'm going to help you with a final expense policy today", "Do you have or have you ever had any major medical problems?"

SPEAKER B — CUSTOMER (elderly US citizen, typically 50-85 years old):
- Receives the call, responds to agent questions
- Speaks in SHORT responses: "Yes", "No", "Okay", "Yeah", "Uh-huh", "Mm-hmm", "Right", "I don't know", "I can't", "How much is that?"
- May give longer responses when telling personal stories or asking questions
- May sound confused, uncertain, or hard of hearing
- May go off-topic to discuss family, health, or unrelated matters

SPEAKER C — [BANK IVR] (automated bank phone system — only present during bank verification):
- Appears ONLY if agent performs a 3-way bank verification call
- Is automated/robotic: "Thank you for calling Bank of America", "Please enter your account number", "For quality purposes your call may be recorded", "Please hold while we transfer you", "I'm sorry, I didn't understand that", hold music descriptions, etc.
- Is NEVER the customer speaking, even though it comes from the customer's bank

SPEAKER LABELING RULES:
1. IDENTIFY the AGENT first — the agent introduces themselves by name and company ("My name is [NAME] calling from...", "This is [NAME] from Taurus...", "Am I speaking with [NAME]?"). This is the most reliable signal. Note: the customer may say "Hello?" or "Yes?" when they first answer the phone BEFORE the agent's introduction — that short response is CUSTOMER, not AGENT.
2. Short acknowledgements ("Hello?", "Yes", "Okay", "Uh-huh", "Yeah", "Right", "Mm-hmm", "Who is this?") → CUSTOMER
3. Long explanatory sentences about insurance, health questions, banking, reading back application details → AGENT
4. Anything that sounds like an automated phone system during a bank call → [BANK IVR]
5. Do NOT change, add, or remove any words from the transcript
6. Start a new labeled segment whenever the speaker changes
7. Every segment must start with exactly: "AGENT: " or "CUSTOMER: " or "[BANK IVR] "

Produce the labeled transcript and include it as the "diarized_transcript" field in your JSON output (see output format below). Use your labeled transcript as the basis for all QA scoring.

═══════════════════════════════════════════════════════════════
STEP 2 — QA EVALUATION
Use your labeled transcript from Step 1 to evaluate the call.
═══════════════════════════════════════════════════════════════

EXPECTED CALL PROCESS (use this as your scoring reference):
The standard Final Expense sales call follows this sequence:
 PHASE 1 — INTRODUCTION & RAPPORT: Professional greeting, confirm prospect identity, build trust, ask light personal questions, show empathy, acknowledge family situation.
 PHASE 2 — NEEDS ANALYSIS: Identify who would be responsible for funeral costs, current savings, desired coverage amount, budget range. Verify personal info: full name, DOB, address, phone, email, citizenship, tobacco use, height & weight.
 PHASE 3 — HEALTH QUALIFICATION: Ask complete carrier health questionnaire including (a) MEDICATIONS — ask which medicines the customer is currently taking AND what each is taken for; (b) HEALTH CONDITIONS — ask about major medical problems covering both PRESENT and PAST conditions. Required conditions to cover: dementia, cancer, heart attack, stroke, COPD, kidney dialysis, and CHF. Proper phrasing: "Do you have, or have you ever had, any major medical problems?" Additional conditions: diabetes (insulin vs non-insulin), oxygen use, hospitalizations, surgeries, mobility status.
 PHASE 4 — PLAN PRESENTATION: Present 2-3 coverage options with exact monthly premiums. Explain fixed premium, lifetime coverage, no rate increases, cash value (if applicable), tax-free benefit. Quote the specific coverage and premium the customer qualifies for.
 PHASE 5 — CLOSE: Ask for the sale. Use assumptive or alternative closing techniques.
 PHASE 6 — BANK INFORMATION COLLECTION: Explain monthly automatic draft. Collect: bank name, routing number, account number, account type (checking/savings), draft date (1st–28th). Confirm customer owns the account, confirm no restrictions on account, confirm SSN.
 PHASE 7 — BANK VERIFICATION (MANDATORY): Perform one of: (a) Online bank verification — send secure link, client logs into bank portal; (b) IVR verification — three-way call to bank's automated system; (c) Live bank call — three-way with bank representative to confirm account holder name, account number, routing number, account active and in good standing.
 PHASE 8 — APPLICATION SUBMISSION & RECORDED CONSENT: Review full application details (coverage, premium, draft date, beneficiary, bank, address). Obtain recorded authorization — voice signature confirming today's date, customer's full name, customer's DOB, customer's SSN, consent to draft, and consent to electronic communication.
 PHASE 9 — END-OF-CALL RELATIONSHIP: Set 3-day follow-up appointment. Set security question ("favorite color"). Provide agent's full name, direct phone, office number, email. Thank customer and reassure protection.

═══════════════════════════════════════════════════════════════
LAYER 1 — COMPLIANCE CHECKS (17 items)
Any single FAIL here = COMPLIANCE_FAIL disposition (overrides everything)
Mark each as "pass", "fail", or "na" (not applicable to this call type)
═══════════════════════════════════════════════════════════════

C1  closer_consent — The closer (agent) obtains verbal consent of sale from the customer. The customer must verbally confirm all THREE of the following on the call: (1) say YES — a clear verbal agreement to purchase, (2) state their full name, and (3) state today's date. All three elements are required for a "pass". If any of the three is missing, mark "fail". This is NOT about recording disclosure — it is about the customer's spoken consent to the sale itself. Mark "na" only if no sale was reached on the call.

C2  agent_identity — Agent stated their full name AND company name within the first 90 seconds of the call. Agent gives a proper introduction at the beginning of the call.

C3  carrier_named — The actual insurance carrier name was stated (e.g., "American Amicable", "Mutual of Omaha", "CUNA Mutual"). Not just "the company" or "we".

C4  product_type_stated — Agent explicitly said "life insurance policy" or "whole life insurance" or "final expense insurance". The customer must understand they are buying life insurance, not a "benefit" or "program".

C5  health_questions_complete — Agent asks COMPLETE and ACCURATE health questions. Two mandatory components:
    (a) MEDICATIONS — Agent must ask the customer which medications they are currently taking AND what each medication is taken for. A vague "any medications?" without asking the purpose of each medication is a FAIL.
    (b) HEALTH CONDITIONS — Agent must ask about major medical problems covering BOTH current AND past conditions. Required conditions to screen: dementia, cancer, heart attack, stroke, COPD, kidney dialysis, and CHF. The question must be phrased (or equivalent) as "Do you have, or have you ever had, any major medical problems?" — not just "are you healthy?" or only asking about current conditions. Also must cover: diabetes (and whether on insulin), oxygen use, hospitalizations, surgeries, and mobility status.
    Mark "pass" only if BOTH medications AND health conditions sections are addressed with appropriate depth. Mark "fail" if medications are skipped, medication purpose is not asked, health conditions are only asked about present (not past), or the required conditions list is not covered. Mark "na" if the call is too short to reach underwriting questions.

C6  proper_quote — Agent provides a proper quote according to the customer's health conditions. The exact monthly premium dollar amount was clearly stated and confirmed with the customer. Not vague ("around thirty" is fail, "$32.50 per month" is pass).

C7  coverage_amount — The exact death benefit/coverage amount was clearly stated and confirmed. Must be a specific dollar figure. Customer agrees to the coverage.

C8  draft_date_confirmed — Agent confirms the draft date (payment date) clearly with the customer.

C9  end_of_call_consent — Agent obtains proper recorded authorization at the end of the call. Required confirmations: (1) today's date, (2) customer's full name, (3) customer's date of birth, (4) customer's SSN, (5) customer's consent to bank draft, and (6) customer's consent to electronic communication. All six items are required for a full pass. Mark "partial_pass" is not available — if any of the 6 items is missing, mark "fail". Mark "na" only if no sale was made.

C10 waiting_period — If this is a graded/modified policy, the 2-year waiting/graded period was disclosed. If the policy is immediate coverage (no graded period), mark "na".

C11 application_info_collected — Agent collects and verifies all required application information across two phases:
    PERSONAL INFO (Phase 2): Customer's full name with middle initial, date of birth, complete address, phone number, email, citizenship status, tobacco use, height & weight, state of birth.
    BANK & APPLICATION INFO (Phase 6): Bank name, routing number, account number, account type (checking/savings), draft date (1st–28th), confirmation that the customer owns the account, confirmation that there are no restrictions on the account, SSN, beneficiary name + relationship + beneficiary DOB, doctor's name, any alternate phone number.
    BANK VERIFICATION (Phase 7 — MANDATORY): Agent must perform one of: (a) online bank verification link, (b) IVR three-way call to bank's automated line, or (c) live three-way call with a bank representative. If no bank verification step was performed at all, this is a FAIL regardless of whether bank info was collected.
    Mark "pass" if substantially all personal info AND bank info were collected AND some form of bank verification was performed. Mark "fail" if multiple critical items were skipped OR bank verification was entirely omitted.

C12 customer_not_on_dnc — Customer should not be on the Do Not Call list. If at any point the customer says "take me off your list", "don't call me again", or similar DNC request, the agent MUST honor it immediately. If no DNC request was made, mark "na".

C13 customer_not_aggressive — Customer should not be overtly aggressive or hostile. If aggressive and agent continues the sale anyway, mark "fail". If calm/cooperative, mark "pass".

C14 customer_not_disinterested — Customer should not clearly state they are NOT interested as a final answer. If firm rejection and agent pushes past it, mark "fail". If customer shows engagement, mark "pass".

C15 no_pushy_sale — Agent should NOT use high-pressure tactics, create urgency/confusion, or refuse to take no for an answer. Mark "pass" if respectful. Mark "fail" if manipulative.

C16 appropriate_language — Agent should NOT use inappropriate, rude, or unprofessional language. Mark "pass" if professional throughout.

C17 customer_not_abusive — Customer should not be abusive toward the agent. If abusive and agent continues anyway, mark "fail". If no abuse, mark "pass".

═══════════════════════════════════════════════════════════════
LAYER 2 — SALES QUALITY SCORES (7 categories, 1-10 each)
Use the FULL range. Do NOT inflate scores. A score of 10 should be exceptional and rare.
═══════════════════════════════════════════════════════════════

S1  opening (1-10): Professional greeting, warm tone, clear purpose stated, initial rapport. Score 1-3 if robotic/scripted. Score 4-6 if adequate. Score 7-8 if smooth and engaging. Score 9-10 only if genuinely exceptional.

S2  discovery (1-10): Needs discovery — funeral cost responsibility, savings, coverage amount, budget, family situation, health concerns, personal verification, medications (name + purpose), health conditions (past + present, full conditions list). Score 1-3 if skipped. Score 4-6 if basic questions asked. Score 7-8 if thorough and empathetic. Score 9-10 only if masterful.

S3  presentation (1-10): Explained benefits clearly in senior-friendly terms, tied features to customer's specific needs, no jargon, appropriate quote. Score 1-3 if scripted with no personalization. Score 4-6 if adequate. Score 7-8 if compelling and personalized. Score 9-10 only if exceptional.

S4  objection_handling (1-10): Handled pushback, price objections, "I need to think about it", "let me talk to my kids". Score 1-3 if folded or became pushy. Score 4-6 if addressed concerns adequately. Score 7-8 if skillful reframing. Score 9-10 only if turned objections into enthusiastic buy-in. If no objections, max score is 7.

S5  closing (1-10): Asked for the sale, collected complete bank info, performed bank verification, obtained full recorded authorization (date, name, DOB, SSN, draft consent, electronic communication consent), set 3-day follow-up, established security question. Score 1-3 if never asked for sale. Score 4-6 if asked but missed steps. Score 7-8 if solid execution. Score 9-10 only if flawless end-to-end close.

S6  soft_skills (1-10): Empathy with seniors — patience, respect, appropriate pace, sensitivity around death/mortality, genuine caring tone. Score 1-3 if dismissive/impatient. Score 4-6 if polite but mechanical. Score 7-8 if genuinely warm. Score 9-10 only if exceptional.

S7  call_control (1-10): Maintained conversation flow, redirected tangents politely, managed time, kept momentum. Score 1-3 if lost control entirely. Score 4-6 if adequate flow. Score 7-8 if smooth. Score 9-10 only if seamlessly guided a complex conversation to a natural close.

total_score = round((S1 + S2 + S3 + S4 + S5 + S6 + S7) / 70 * 100)

═══════════════════════════════════════════════════════════════
DISPOSITION (assign exactly ONE, in this priority order):
═══════════════════════════════════════════════════════════════

1. COMPLIANCE_FAIL — Any C1-C17 marked as "fail"
2. VOID_RISK — A sale was made BUT with misrepresentation, customer confusion, promises the policy doesn't support, or clear pressure/disinterest
3. EXCELLENT — compliance_pass=true AND total_score >= 90
4. GOOD — compliance_pass=true AND total_score 75-89
5. AVERAGE — compliance_pass=true AND total_score 60-74
6. POOR — compliance_pass=true AND total_score < 60

CALIBRATION: Excellent should be rare (~10-15% of good calls). Average agent = ~5-6 per category = ~70 total score.
coaching_notes MUST reference specific things the agent said or did. No generic advice.

═══════════════════════════════════════════════════════════════
LAYER 3 — CALL DATA EXTRACTION
═══════════════════════════════════════════════════════════════

E1  customer_name — Full name of the CUSTOMER (person being called, not the agent). The agent asks "Am I speaking with [NAME]?" — that name is the customer. Return null if unclear.
E2  closer_name — Full name of the AGENT (person making the call). They say "My name is [NAME] calling from...". Return null if unclear.
E3  is_sale — true if customer agreed to proceed and application process began. false if call ended without commitment.
E4  sale_amount — Death benefit in dollars (e.g., 10000). null if no sale or not stated.
E5  monthly_premium — Monthly premium in dollars (e.g., 32.50). null if no sale or not stated.
E6  carrier_name — Insurance carrier name (e.g., "American Amicable"). null if not mentioned.
E7  policy_type — "Whole Life", "Term", "Graded", "Modified", or "Unknown". null if unclear.
E8  customer_state — 2-letter US state code if mentioned. null if not mentioned.

═══════════════════════════════════════════════════════════════
OUTPUT FORMAT — Return ONLY this JSON, no markdown, no preamble, no explanation:
The "diarized_transcript" field MUST be populated with the speaker-labeled transcript from Step 1.
═══════════════════════════════════════════════════════════════

{
  "diarized_transcript": "AGENT: Hello, my name is...\nCUSTOMER: Yes?\nAGENT: ...",
  "disposition": "EXCELLENT|GOOD|AVERAGE|POOR|COMPLIANCE_FAIL|VOID_RISK",
  "total_score": 0,
  "compliance_pass": true,
  "compliance_checks": {
    "C1_closer_consent": "pass|fail|na",
    "C2_agent_identity": "pass|fail|na",
    "C3_carrier_named": "pass|fail|na",
    "C4_product_type_stated": "pass|fail|na",
    "C5_health_questions_complete": "pass|fail|na",
    "C6_proper_quote": "pass|fail|na",
    "C7_coverage_amount": "pass|fail|na",
    "C8_draft_date_confirmed": "pass|fail|na",
    "C9_end_of_call_consent": "pass|fail|na",
    "C10_waiting_period": "pass|fail|na",
    "C11_application_info_collected": "pass|fail|na",
    "C12_customer_not_on_dnc": "pass|fail|na",
    "C13_customer_not_aggressive": "pass|fail|na",
    "C14_customer_not_disinterested": "pass|fail|na",
    "C15_no_pushy_sale": "pass|fail|na",
    "C16_appropriate_language": "pass|fail|na",
    "C17_customer_not_abusive": "pass|fail|na"
  },
  "compliance_failures": [],
  "score_breakdown": {
    "opening": 0,
    "discovery": 0,
    "presentation": 0,
    "objection_handling": 0,
    "closing": 0,
    "soft_skills": 0,
    "call_control": 0
  },
  "extracted_data": {
    "customer_name": null,
    "closer_name": null,
    "is_sale": false,
    "sale_amount": null,
    "monthly_premium": null,
    "carrier_name": null,
    "policy_type": null,
    "customer_state": null
  },
  "coaching_notes": "Specific actionable feedback referencing what happened in THIS call...",
  "top_issue": "The single most important thing this agent should improve",
  "strengths": ["Strength 1 specific to this call", "Strength 2"],
  "improvements": ["Improvement 1 specific to this call", "Improvement 2"],
  "void_risk_reason": null
}

CRITICAL REMINDERS:
- Return ONLY the JSON object above. No text before or after.
- diarized_transcript MUST contain the full labeled transcript with AGENT:/CUSTOMER:/[BANK IVR] prefixes.
- compliance_failures array should list the check keys that failed (empty array if all pass).
- void_risk_reason should be null unless disposition is VOID_RISK.
- coaching_notes must be 2-4 sentences referencing specific moments in this call.
- strengths and improvements arrays should each have 2-4 items.
- is_sale must be true ONLY if the customer explicitly agreed and the application process began.
PROMPT;
    }
}
