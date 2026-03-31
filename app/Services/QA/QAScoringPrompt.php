<?php

namespace App\Services\QA;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QAScoringPrompt
{
    // Storage file for the custom prompt override
    private const FILE_SCORED = 'qa_prompt_scored.txt';

    /**
     * Return the path to the prompt override file (stored in storage/app/).
     */
    public static function promptFilePath(): string
    {
        return storage_path('app/' . self::FILE_SCORED);
    }

    /**
     * Check if a custom prompt file exists.
     */
    public static function hasCustomPrompt(string $type = 'scored'): bool
    {
        return file_exists(self::promptFilePath());
    }

    /**
     * Read the current active prompt template (custom if set, else built-in).
     * Placeholders used in stored templates:
     *   {{TRANSCRIPT}}        → the diarized transcript
     *   {{DURATION_MINUTES}}  → rounded duration in minutes
     *   {{DURATION_SECONDS}}  → raw seconds
     */
    public static function getTemplate(string $type = 'scored'): string
    {
        $path = self::promptFilePath();
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if ($content !== false && trim($content) !== '') {
                return $content;
            }
        }

        // No custom template — generate the default with placeholder markers.
        $transcriptSentinel = '___QA_TRANSCRIPT_PLACEHOLDER___';
        $text = self::build($transcriptSentinel, 0);
        $text = str_replace($transcriptSentinel, '{{TRANSCRIPT}}', $text);
        $text = preg_replace('/0\.0 minutes \(0 seconds\)/', '{{DURATION_MINUTES}} minutes ({{DURATION_SECONDS}} seconds)', $text);

        return $text;
    }

    /**
     * Save a custom prompt template to disk.
     */
    public static function saveTemplate(string $type, string $content): void
    {
        file_put_contents(self::promptFilePath(), $content);
        Log::info('[QAScoringPrompt] Custom prompt saved');
    }

    /**
     * Reset the prompt back to the built-in default.
     */
    public static function resetTemplate(string $type = 'scored'): void
    {
        $path = self::promptFilePath();
        if (file_exists($path)) {
            unlink($path);
        }
        Log::info('[QAScoringPrompt] Custom prompt reset to default');
    }

    /**
     * Render a stored (or default) template by injecting the actual call data.
     */
    private static function renderTemplate(string $template, string $transcript, int $durationSeconds): string
    {
        $durationMinutes = round($durationSeconds / 60, 1);
        return str_replace(
            ['{{TRANSCRIPT}}', '{{DURATION_MINUTES}}', '{{DURATION_SECONDS}}'],
            [$transcript, $durationMinutes, $durationSeconds],
            $template
        );
    }

    /**
     * Build the complete QA scoring prompt with the call transcript injected.
     */
    public static function build(string $diarizedTranscript, int $durationSeconds): string
    {
        // If a custom template file exists, use it
        $path = self::promptFilePath('scored');
        if (file_exists($path)) {
            $template = file_get_contents($path);
            if ($template !== false && trim($template) !== '') {
                return self::renderTemplate($template, $diarizedTranscript, $durationSeconds);
            }
        }

        $durationMinutes = round($durationSeconds / 60, 1);

        return <<<PROMPT
You are an expert Quality Assurance analyst for a life insurance outbound call center. You are evaluating a recorded COLD OUTBOUND SALES CALL — this is the FIRST contact between the closer and the customer. The closer initiated this call; the customer did not request it. The closer is a sales representative whose job is to present and sell a life insurance policy in a single call.

SPEAKER ROLES:
- AGENT: the insurance closer who made the outbound call. They introduce themselves, ask health questions, present the product, and close the sale. (Referred to as "Closer" throughout this evaluation.)
- CUSTOMER: the senior citizen (typically age 50-85) who received the call. They respond to the closer's questions with short answers and may share personal concerns.
- [BANK IVR]: automated bank phone system — appears ONLY during a 3-way bank verification call. Not the customer speaking.

CALL DURATION: {$durationMinutes} minutes ({$durationSeconds} seconds)

TRANSCRIPT:
---
{$diarizedTranscript}
---

SPEAKER LABEL DEFINITIONS:
- AGENT (= CLOSER): the insurance sales representative making the outbound call. They introduce themselves by name and company, present the insurance product, ask health questions, collect personal and banking information, and request the sale.
- CUSTOMER: the senior citizen (typically age 50-85) who received the call. They respond to the closer's questions, give short replies, and may share personal stories or concerns.
- [BANK IVR]: automated bank phone system audio during a three-way bank verification call. These lines are NOT the customer speaking.
TRUST the AGENT/CUSTOMER labels as written. Score based on what the AGENT (CLOSER) lines say and how the CUSTOMER responds.

IMPORTANT — COLD CALL CONTEXT: Because this is a cold outbound call, the customer was NOT expecting to be sold to. It is NORMAL and EXPECTED for customers to be hesitant, ask questions, or express mild reluctance before agreeing. Doing rebuttals is a standard and required part of a cold call — do NOT penalize rebuttals. Only mark C10 (agent_handles_objections) as fail if the closer becomes excessively aggressive, refuses to accept a FIRM repeated refusal, or uses manipulative high-pressure tactics that go beyond normal rebuttal technique.

EXPECTED CALL PROCESS (use this as your scoring reference):
The standard life insurance sales call follows this sequence:
 PHASE 1 — INTRODUCTION & RAPPORT: Professional greeting, confirm prospect identity, build trust, ask light personal questions, show empathy, acknowledge family situation. Closer states their name and company name at some point in the call.
 PHASE 2 — NEEDS ANALYSIS: Identify who would be responsible for funeral costs, current savings, desired coverage amount, budget range. Verify personal info: full name, DOB, address, phone, citizenship, tobacco use, height & weight.
 PHASE 3 — HEALTH QUALIFICATION: Ask about medications the customer is taking and any health conditions (past and present). This does not need to be an exhaustive list — any reasonable medication and health questions qualify.
 PHASE 4 — PLAN PRESENTATION: Present coverage options. State the monthly premium (exact amount or a range like "$50–$70/month") and the coverage/death benefit amount. Name the actual insurance carrier. Make clear the customer is buying life insurance (not just a "benefit" or "program").
 PHASE 5 — CLOSE: Ask for the sale. Use assumptive or alternative closing techniques.
 PHASE 6 — BANK INFORMATION COLLECTION: Explain monthly automatic draft. Collect: bank name, routing number, account number, account type (checking/savings), draft date. Confirm customer owns the account and SSN.
 PHASE 7 — APPLICATION SUBMISSION & RECORDED CONSENT: Review full application details (coverage, premium, draft date, beneficiary, bank, address). Obtain recorded authorization — voice signature confirming today's date, customer's full name, customer's DOB, customer's SSN, consent to draft, and consent to electronic communication.

EVALUATION INSTRUCTIONS:

You MUST evaluate this call using TWO scoring layers, then assign a disposition.

═══════════════════════════════════════════════════════════════
LAYER 1 — COMPLIANCE CHECKS (11 items)
Any single FAIL here = COMPLIANCE_FAIL disposition (overrides everything)
Mark each as "pass", "fail", or "na" (not applicable to this call type)
These checks evaluate the CLOSER's conduct, not the customer's behavior.
═══════════════════════════════════════════════════════════════

--- Call Handling Compliance ---

C1  agent_identity — The closer stated their full name AND the company name at any point during the call. There is NO time restriction — it can be stated in the intro, mid-call, or at the end. Mark "pass" if both are stated anywhere in the call. Mark "fail" only if neither name nor company is mentioned at all.

C2  carrier_named — The actual insurance carrier name was stated at some point in the call (e.g., "American Amicable", "Mutual of Omaha", "CUNA Mutual", "Americo", "AIG", "Corebridge", etc.). Note: AIG and Corebridge Financial are the same carrier. Not just "the company" or "we" — the real carrier name must be mentioned. Mark "na" if no presentation was reached.

C3  product_type_stated — The closer explicitly stated the customer is getting a "life insurance policy" or "whole life insurance" or "life insurance" at some point in the call. The customer must understand they are buying life insurance, not just a "benefit", "program", or "protection plan". Mark "na" if no presentation was reached.

C4  health_questions_complete — The closer asked about the customer's health. Two components must be present:
    (a) MEDICATIONS — Asked what medications the customer is taking (any form of medication question qualifies — does not need to be a detailed per-medication purpose inquiry; a general "what medications are you on?" is acceptable).
    (b) HEALTH CONDITIONS — Asked about the customer's medical conditions or health history (current and/or past). Any reasonable health screening question qualifies. The screening does NOT need to cover a specific list of conditions.
    Mark "pass" if BOTH (a) and (b) were addressed in any form. Mark "fail" only if health questions were entirely skipped. Mark "na" if the call is too short to reach underwriting questions.

C5  quote_and_coverage — The closer provided both a premium amount AND a coverage/death benefit amount. The premium may be stated as an exact figure (e.g., "$52/month") OR as a range (e.g., "$50–$70/month" or "between $50 and $70"). The coverage amount may also be a range or exact figure. Be lenient — do not fail for minor vagueness as long as both a premium figure and a coverage figure were communicated. Mark "na" if no sale was reached.

C6  draft_date_confirmed — The closer confirmed the draft date (the day of the month the premium will be debited) clearly with the customer. Mark "na" if no sale was made.

C7  end_of_call_consent — The closer obtains proper recorded authorization near the end of the call. Required confirmations: (1) today's date, (2) customer's full name, (3) customer's date of birth, (4) customer's SSN, (5) customer's consent to bank draft, and (6) customer's consent to electronic communication. All six items are required for a full pass. If any of the 6 items is missing, mark "fail". Mark "na" only if no sale was made.

--- Application Requirements Compliance ---

C8  application_info_collected — The closer collects the core application information needed to process the policy:
    PERSONAL INFO: Customer's full name, date of birth, complete address, phone number, citizenship status, tobacco use, height & weight.
    BANK INFO: Bank name, routing number, account number, account type (checking/savings), draft date, customer owns the account, SSN, beneficiary name and relationship.
    Note: Email address is NOT required on this call. Bank verification (IVR or 3-way call) is NOT required on this call type.
    Mark "pass" if substantially all personal and bank info were collected. Mark "fail" if multiple critical items (name, DOB, bank account info, SSN, beneficiary) were skipped.

--- Behavioral Compliance ---

C9  customer_not_on_dnc — If at any point the customer says "take me off your list", "don't call me again", "put me on the do-not-call list", or any similar DNC request, the closer MUST honor it immediately and stop the sales pitch. Mark "fail" if the closer ignored a clear DNC request and continued the pitch. Mark "na" if no DNC request was made.

C10 agent_handles_objections — The closer must handle objections and rebuttals professionally. On a cold call, rebuttals are expected and required — this is NOT a reason to fail. Mark "fail" ONLY if the closer: (a) continued pushing after the customer gave a FIRM, repeated, unambiguous final refusal (said "no" multiple times clearly), OR (b) used overtly manipulative, deceptive, or high-pressure tactics that went far beyond normal rebuttal technique. A closer doing multiple rebuttals on a reluctant customer is NORMAL and should NOT fail this check.

C11 appropriate_language — The closer must NOT use inappropriate, rude, unprofessional, or abusive language during the call. Mark "pass" if the closer was professional throughout. Mark "fail" if the closer used any inappropriate language toward the customer.

═══════════════════════════════════════════════════════════════
LAYER 1B — INFORMATIONAL NOTE (not a compliance check, does not affect disposition)
═══════════════════════════════════════════════════════════════

INFO_waiting_period — If this appears to be a graded/modified policy (limited benefit in first 2 years), note whether the waiting period was disclosed to the customer. This is for informational tracking only and will NOT cause a COMPLIANCE_FAIL regardless of the result. Values: "disclosed", "not_disclosed", "not_applicable" (immediate coverage policy or no sale).

═══════════════════════════════════════════════════════════════
LAYER 2 — SALES QUALITY SCORES (7 categories, 1-10 each)
Sales quality contributes only 10 points to the total score (compliance is the primary factor).
Use the FULL range. Do NOT inflate scores. A score of 10 should be exceptional and rare.
═══════════════════════════════════════════════════════════════

S1  opening (1-10): Professional greeting, warm tone, clear purpose stated, built initial rapport quickly. Did the closer hook the customer's attention within the first 30 seconds? Score 1-3 if robotic/scripted with no warmth. Score 4-6 if adequate but unremarkable. Score 7-8 if smooth and engaging. Score 9-10 only if genuinely exceptional rapport-building.

S2  discovery (1-10): Needs discovery and due diligence — asked about who would be responsible for funeral costs, current savings, desired coverage amount, budget range, family situation, health concerns. Also covered personal verification (tobacco, height/weight, citizenship). Asked about medications and health conditions. Listened more than talked during this phase. Score 1-3 if skipped discovery entirely. Score 4-6 if asked basic questions. Score 7-8 if thorough and empathetic. Score 9-10 only if masterful probing that uncovered deep emotional needs and covered all qualification areas completely.

S3  presentation (1-10): Product presentation — explained benefits clearly in terms the senior can understand, tied features to the customer's specific needs identified in discovery. Did NOT use jargon. Provided a quote appropriate to the customer's health. Score 1-3 if read a script with no personalization. Score 4-6 if adequate explanation. Score 7-8 if compelling and personalized. Score 9-10 only if the presentation was so clear and emotionally resonant that the value was undeniable.

S4  objection_handling (1-10): How well did the closer handle pushback, concerns, price objections, "I need to think about it", "let me talk to my kids", etc.? Rebuttals are expected on cold calls — score on QUALITY of rebuttals. Score 1-3 if folded immediately at first objection with no rebuttal attempt. Score 4-6 if addressed concerns adequately. Score 7-8 if skillful reframing without pressure. Score 9-10 only if turned strong objections into enthusiastic buy-in naturally. If no objections were raised, score based on prebuttals and proactive concern-addressing (max 7 if no actual objections handled).

S5  closing (1-10): Did the closer ask for the sale? Collected complete bank info? Obtained full recorded authorization (date, name, DOB, SSN, draft consent, electronic communication consent)? Did the closer set a follow-up and establish a security question? Score 1-3 if never asked for the sale or only asked once timidly. Score 4-6 if asked but technique was basic and multiple consent steps were missed. Score 7-8 if used multiple closing techniques effectively and completed the application properly. Score 9-10 only if demonstrated masterful assumptive/alternative closing, full recorded authorization, follow-up set, and the entire end-of-call sequence executed flawlessly.

S6  soft_skills (1-10): Empathy with seniors — patience, respect, appropriate pace, not rushing, acknowledging concerns about death/mortality sensitively, genuine caring tone. Score 1-3 if dismissive, impatient, or insensitive. Score 4-6 if polite but mechanical. Score 7-8 if genuinely warm and patient. Score 9-10 only if the closer made the senior feel truly heard, respected, and cared for.

S7  call_control (1-10): Maintained control of the conversation flow, redirected tangents politely, managed time well, kept momentum toward the close without creating pressure. Score 1-3 if lost control entirely or let customer ramble for minutes. Score 4-6 if adequate flow. Score 7-8 if smooth transitions and good pacing. Score 9-10 only if seamlessly guided a complex conversation to a natural close.

SCORING FORMULA:
total_score = round((S1 + S2 + S3 + S4 + S5 + S6 + S7) / 70 * 100)

This gives a 0–100 range based purely on sales performance. Compliance issues are flagged separately and do NOT override the disposition — every call gets a score-based disposition reflecting the closer's actual performance.

CRITICAL: total_score MUST ALWAYS be calculated from the formula above. Do NOT set total_score to 0 because compliance failed. Score the closer's sales performance independently regardless of compliance result.

═══════════════════════════════════════════════════════════════
DISPOSITION (assign exactly ONE based on total_score, in this priority order):
═══════════════════════════════════════════════════════════════

1. VOID_RISK — A sale was made BUT there was misrepresentation, customer confusion about what they bought, the closer made promises the policy doesn't support, or the customer was clearly coerced.
2. EXCELLENT — total_score >= 90
3. GOOD — total_score 75-89
4. AVERAGE — total_score 60-74
5. POOR — total_score < 60

NOTE: compliance_pass and compliance_failures are tracked separately. A call can have disposition=GOOD and compliance_pass=false simultaneously — this means the closer performed well commercially but missed a compliance step. Do NOT set disposition=COMPLIANCE_FAIL. There is no such disposition anymore.

CALIBRATION NOTES:
- Excellent should be rare (~10-15% of calls). Do NOT give Excellent just because nothing went wrong.
- Use the full 1-10 range for sales scores. An average closer on an average call should score 5-6 per category, yielding a total_score around 70-75.
- A "good" compliant call with no mistakes but nothing special = total_score around 70-75.
- coaching_notes MUST reference specific things the closer said or did (or failed to do). No generic advice.
- If the call was too short for a full evaluation on some criteria, score what you can observe and note limitations.

═══════════════════════════════════════════════════════════════
LAYER 3 — CALL DATA EXTRACTION
Extract these from the transcript. These are CRITICAL for business records.
═══════════════════════════════════════════════════════════════

E1  customer_name — The full name of the CUSTOMER (the prospect/senior being CALLED). This is the person the closer is trying to sell to. Key clues:
    - The closer asks "Am I speaking with [NAME]?" or "Is this [NAME]?" — that name is the CUSTOMER.
    - The closer addresses them as "Mr./Mrs./Ms. [NAME]" throughout the call.
    - Do NOT confuse the closer with the customer. The person who introduces themselves as calling FROM a company is the CLOSER, not the customer.
    Return null if unclear.

E2  closer_name — The full name of the CLOSER (the salesperson MAKING the call). This is the person selling insurance. Key clues:
    - They say "My name is [NAME]" or "This is [NAME] calling from [COMPANY]".
    - They are the one presenting the insurance product and asking for the sale.
    Return null if unclear.

E3  is_sale — Was a sale/application completed during this call? A sale means the customer agreed to proceed, gave personal details for the application, and the closer processed it. If the call ended with "I'll think about it" or no commitment, is_sale = false.

E4  sale_amount — The total coverage/death benefit amount in dollars (e.g., 10000 for $10,000 coverage). Return null if no sale or amount not stated.

E5  monthly_premium — The monthly premium amount in dollars (e.g., 32.50). If a range was quoted, use the midpoint. Return null if no sale or not stated.

E6  carrier_name — The insurance carrier name (e.g., "American Amicable", "Mutual of Omaha", "CUNA Mutual", "Americo", "Corebridge", "AIG"). Return null if not mentioned.

E7  policy_type — The type of policy: "Whole Life", "Term", "Graded", "Modified", or "Unknown". Return null if not clear.

E8  customer_state — The US state the customer lives in if mentioned (2-letter code like "TX", "FL", "OH"). Return null if not mentioned.

═══════════════════════════════════════════════════════════════
OUTPUT FORMAT — Return ONLY this JSON, no markdown, no preamble, no explanation:
═══════════════════════════════════════════════════════════════

{
  "disposition": "EXCELLENT|GOOD|AVERAGE|POOR|VOID_RISK",
  "total_score": 72,
  "compliance_pass": true,
  "compliance_checks": {
    "C1_agent_identity": "pass|fail|na",
    "C2_carrier_named": "pass|fail|na",
    "C3_product_type_stated": "pass|fail|na",
    "C4_health_questions_complete": "pass|fail|na",
    "C5_quote_and_coverage": "pass|fail|na",
    "C6_draft_date_confirmed": "pass|fail|na",
    "C7_end_of_call_consent": "pass|fail|na",
    "C8_application_info_collected": "pass|fail|na",
    "C9_customer_not_on_dnc": "pass|fail|na",
    "C10_agent_handles_objections": "pass|fail|na",
    "C11_appropriate_language": "pass|fail|na"
  },
  "informational_notes": {
    "waiting_period": "disclosed|not_disclosed|not_applicable"
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
  "informational_notes": {
    "waiting_period": "disclosed|not_disclosed|not_applicable"
  },
  "coaching_notes": "Specific actionable feedback referencing what happened in THIS call...",
  "top_issue": "The single most important thing this closer should improve",
  "strengths": ["Strength 1 specific to this call", "Strength 2"],
  "improvements": ["Improvement 1 specific to this call", "Improvement 2"],
  "void_risk_reason": null
}

CRITICAL REMINDERS:
- Return ONLY the JSON object above. No text before or after.
- Every value must be filled in based on the actual transcript.
- compliance_failures array should list the check keys that failed (e.g., "C1_agent_identity"). Empty array if all pass.
- void_risk_reason should be null unless disposition is VOID_RISK.
- coaching_notes must be 2-4 sentences referencing specific moments in the call.
- strengths and improvements arrays should each have 2-4 items.
- extracted_data MUST always be present. Use null for any field you cannot determine from the transcript.
- is_sale must be true ONLY if the customer explicitly agreed and the application process began.
- informational_notes.waiting_period does NOT affect compliance_pass or disposition — it is for tracking only.
PROMPT;
    }
}

