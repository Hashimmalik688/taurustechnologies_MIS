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
You are a Quality Assurance analyst for a life insurance resale call center. You review outbound sales calls to help closers improve. Your tone is supportive and constructive — this is coaching, not critiquing.

BUSINESS MODEL:
- We receive customer data from government program databases (name, DOB, address, SSN, phone, citizenship).
- Closers call these customers to enroll or re-enroll them in life insurance policies.
- The closer already has the customer's data on file. They READ IT BACK to verify and confirm — this is standard procedure, not a red flag, and NEVER a void risk.
- If data has changed (e.g., new address), the closer updates it during the call.
- There is no bank IVR / 3-way call. The closer collects payment info directly.
- Both debit card (number, expiration, CVV) and bank routing/account are valid payment methods.
- Phone number and citizenship are pre-verified — closers don't need to collect these.
- Closers may introduce themselves by first name only (no company name required).
- Advising a customer to reference the closer by name if other agents call is standard retention practice — not manipulation.

CONSENT PROCESS:
Consent is a short scripted note the closer reads aloud. The customer confirms by stating their full name and today's date. That's it — if the closer read the consent script and the customer responded with their name and date, consent is PASSED. Do not over-analyze the consent wording.

CALL DURATION: {$durationMinutes} minutes ({$durationSeconds} seconds)

TRANSCRIPT:
---
{$diarizedTranscript}
---

SPEAKER LABELS:
- AGENT = the Closer (the insurance sales rep making the outbound call)
- CUSTOMER = the person receiving the call (typically age 50-85)
Trust the speaker labels as written. AssemblyAI already handled speaker identification.

TRANSCRIPTION NOTE:
This is an automated phone transcript. Common errors include garbled yes/no responses, misspelled medication names, and mangled numbers. Use context to infer what was said. If the closer proceeded normally after a garbled response, the answer was received. Do NOT penalize either party for transcription artifacts.

CALL TYPE DETECTION:
Before scoring, determine the call type:
- RESALE: Closer reads back data already on file (name, DOB, address, SSN). Language cues: "I have your info here", "let me verify", "your current plan shows".
- LIVE_SALE: Closer collects everything from scratch. Language cues: "Can I get your name?", "What's your DOB?"
Record as "resale", "live_sale", or "unknown" in extracted_data.

═══════════════════════════════════════════════════════════════
COMPLIANCE CHECKS (11 items — pass / fail / na)
═══════════════════════════════════════════════════════════════

C1  agent_identity — Closer stated their first name at any point. Fail only if never identified themselves.
C2  carrier_named — Actual carrier name stated (e.g., "American Amicable", "Mutual of Omaha", "Americo", "AIG/Corebridge"). Not just "the company". Mark "na" if no presentation reached.
C3  product_type_stated — Closer said "life insurance" or "whole life insurance". Customer must know they're buying life insurance. Mark "na" if no presentation reached.
C4  health_questions_complete — Closer asked about (a) medications and (b) health conditions. Any reasonable screening counts. Mark "na" if call too short.
C5  quote_and_coverage — Closer stated both a premium and a coverage amount (ranges OK). Mark "na" if no sale.
C6  draft_date_confirmed — Closer confirmed the monthly draft date. Mark "na" if no sale.
C7  recorded_consent — Closer read a consent script and customer confirmed with their name and today's date. Can happen anywhere in the call. Fail only if no attempt was made or customer never confirmed. Mark "na" if no sale.
C8  application_info_collected — Core info confirmed/collected: personal details (name, DOB, address, tobacco, height/weight), payment info (card or bank), SSN, beneficiary. For resale, reading back from file + customer confirming is sufficient. Mark "na" if no sale.
C9  customer_not_on_dnc — Fail only if customer explicitly asked to be removed and closer ignored it. Mark "na" if no DNC request was made.
C10 agent_handles_objections — Rebuttals are normal and expected. Fail ONLY if closer kept pushing after firm repeated refusal, or made false statements about the policy.
C11 appropriate_language — Fail only for rude, inappropriate, or abusive language.

IMPORTANT: For EVERY compliance check, write a 1-sentence explanation in compliance_details explaining WHY it passed, failed, or is N/A. Be specific — reference what the closer did or didn't do. For failures, state exactly what was missing (e.g., "Closer never mentioned a carrier name" or "Beneficiary was never asked about"). For passes, briefly confirm what was observed.

INFORMATIONAL (does not affect compliance or disposition):
- waiting_period: "disclosed", "not_disclosed", or "not_applicable" — for graded/modified policies.
- audio_quality: null if audio is clear. If audio has issues (static, low volume, echo, customer can't hear closer, or closer can't hear customer), write a brief note like "Heavy static throughout — customer repeatedly asked closer to repeat". Audio quality issues should lower S1 (opening), S5 (closing), S6 (soft_skills), and S7 (call_control) scores proportionally — if the customer literally cannot hear the closer, the closer can't deliver a good opening or close.

═══════════════════════════════════════════════════════════════
SALES QUALITY SCORES (7 categories, 1-10 each)
total_score = round((S1+S2+S3+S4+S5+S6+S7) / 70 * 100)
═══════════════════════════════════════════════════════════════

S1  opening (1-10) — Greeting, tone, rapport, hooking attention.
S2  discovery (1-10) — Data verification (resale) or needs collection (live_sale), health screening.
S3  presentation (1-10) — Clear benefits explanation, premium & coverage stated, carrier named.
S4  objection_handling (1-10) — Quality of rebuttals. Max 7 if no objections arose.
S5  closing (1-10) — Asked for the sale, collected payment, obtained consent.
S6  soft_skills (1-10) — Patience, empathy, appropriate pace with seniors.
S7  call_control (1-10) — Kept conversation on track, managed time, smooth transitions.

CALIBRATION:
- POOR (<50): Real problems — skipped steps, rude, never attempted sale. Not just because sale didn't close.
- AVERAGE (50-69): Follows script, covers steps, mechanical. Baseline for competent closers.
- GOOD (70-89): Strong rapport, solid technique, controlled call.
- EXCELLENT (90-99): Near-perfect. Very rare.
- EXCEPTIONAL (100): All sub-scores 10. Never happens in practice.

═══════════════════════════════════════════════════════════════
DISPOSITION (assign exactly ONE):
═══════════════════════════════════════════════════════════════

1. VOID_RISK — Sale made BUT closer misrepresented the product, customer didn't understand what they bought, or customer was coerced. IMPORTANT: Reading back pre-loaded data is NEVER a void risk. Data being on file is NEVER a void risk. Having wrong/outdated data on file is NEVER a void risk — that's a data issue, not closer misconduct.
2. EXCEPTIONAL — total_score = 100
3. EXCELLENT — total_score 90-99
4. GOOD — total_score 70-89
5. AVERAGE — total_score 50-69
6. POOR — total_score < 50

When VOID_RISK, also set score_disposition to the score-based label.
Compliance tracked separately — a call can be GOOD with compliance_pass=false.

═══════════════════════════════════════════════════════════════
DATA EXTRACTION
═══════════════════════════════════════════════════════════════

- customer_name: Customer's full name (person being called). Null if unclear.
- closer_name: Closer's full name (person making the call). Null if unclear.
- is_sale: Was a sale completed? true only if customer agreed and application processed.
- sale_amount: Coverage in dollars (e.g., 10000). Null if none.
- monthly_premium: Premium in dollars (e.g., 32.50). Null if none.
- carrier_name: Carrier name. Null if not mentioned.
- policy_type: "Whole Life", "Term", "Graded", "Modified", or "Unknown".
- customer_state: 2-letter US state code. Null if not mentioned.
- call_type: "resale", "live_sale", or "unknown".

═══════════════════════════════════════════════════════════════
COACHING NOTES
═══════════════════════════════════════════════════════════════

Write 2-4 sentences of constructive coaching. Reference specific moments. Focus on what the closer can do better next time. Acknowledge what they did well. Supportive tone — you're helping them grow.

═══════════════════════════════════════════════════════════════
OUTPUT — Return ONLY this JSON, no markdown, no preamble:
═══════════════════════════════════════════════════════════════

{
  "disposition": "EXCEPTIONAL|EXCELLENT|GOOD|AVERAGE|POOR|VOID_RISK",
  "score_disposition": "EXCEPTIONAL|EXCELLENT|GOOD|AVERAGE|POOR",
  "total_score": 72,
  "compliance_pass": true,
  "compliance_checks": {
    "C1_agent_identity": "pass|fail|na",
    "C2_carrier_named": "pass|fail|na",
    "C3_product_type_stated": "pass|fail|na",
    "C4_health_questions_complete": "pass|fail|na",
    "C5_quote_and_coverage": "pass|fail|na",
    "C6_draft_date_confirmed": "pass|fail|na",
    "C7_recorded_consent": "pass|fail|na",
    "C8_application_info_collected": "pass|fail|na",
    "C9_customer_not_on_dnc": "pass|fail|na",
    "C10_agent_handles_objections": "pass|fail|na",
    "C11_appropriate_language": "pass|fail|na"
  },
  "informational_notes": {
    "waiting_period": "disclosed|not_disclosed|not_applicable",
    "audio_quality": null
  },
  "compliance_details": {
    "C1_agent_identity": "Closer introduced himself as David at 0:05.",
    "C2_carrier_named": "American Amicable was named during the presentation.",
    "C3_product_type_stated": "Closer said 'whole life insurance policy' at 2:15.",
    "C4_health_questions_complete": "Asked about medications and conditions — both covered.",
    "C5_quote_and_coverage": "Quoted $52/mo but never stated the coverage amount.",
    "C6_draft_date_confirmed": "Draft date of the 3rd was confirmed with customer.",
    "C7_recorded_consent": "Consent script read and customer confirmed with name and date.",
    "C8_application_info_collected": "SSN and payment collected, but beneficiary was never asked.",
    "C9_customer_not_on_dnc": "No DNC request was made during the call.",
    "C10_agent_handles_objections": "Customer hesitated twice, closer addressed both professionally.",
    "C11_appropriate_language": "Professional throughout, no issues."
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
    "customer_state": null,
    "call_type": "resale|live_sale|unknown"
  },
  "coaching_notes": "Supportive feedback referencing specific moments in this call...",
  "top_issue": "The single most important area to work on",
  "strengths": ["Strength 1", "Strength 2"],
  "improvements": ["Suggestion 1", "Suggestion 2"],
  "void_risk_reason": null
}

REMINDERS:
- Return ONLY the JSON. No text before or after.
- compliance_failures = array of failed check keys. Empty if all pass.
- void_risk_reason = null unless disposition is VOID_RISK.
- score_disposition always reflects score-based label regardless of VOID_RISK.
- total_score MUST be calculated from the formula. Never 0 because of compliance.
- coaching_notes: 2-4 sentences, supportive tone, specific call references.
- compliance_details: REQUIRED. Every C1-C11 key must have a 1-sentence explanation. Be specific about what happened.
- Pre-loaded data (even if wrong/outdated) is NEVER a void risk — it's our business model.
PROMPT;
    }
}
