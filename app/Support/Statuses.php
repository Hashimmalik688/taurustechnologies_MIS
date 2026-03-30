<?php

namespace App\Support;

/**
 * Central registry of all status string constants used throughout the application.
 *
 * Organized by domain. Always reference these constants instead of hardcoding
 * status strings to prevent casing bugs and enable IDE refactoring.
 *
 * Status values must match exactly what is stored in the database.
 */
class Statuses
{
    // ═══════════════════════════════════════════════════════════════════
    //  Lead Status  (leads.status column — lowercase)
    // ═══════════════════════════════════════════════════════════════════

    public const LEAD_PENDING      = 'pending';
    public const LEAD_ACCEPTED     = 'accepted';
    public const LEAD_REJECTED     = 'rejected';
    public const LEAD_FORWARDED    = 'forwarded';
    public const LEAD_ACTIVE       = 'active';
    public const LEAD_CLOSED       = 'closed';
    public const LEAD_SALE         = 'sale';
    public const LEAD_DECLINED     = 'declined';
    public const LEAD_RETURNED     = 'returned';
    public const LEAD_TRANSFERRED  = 'transferred';
    public const LEAD_UNDERWRITTEN = 'underwritten';
    public const LEAD_CHARGEBACK   = 'chargeback';

    // ═══════════════════════════════════════════════════════════════════
    //  QA Status  (leads.qa_status column — Title Case)
    // ═══════════════════════════════════════════════════════════════════

    public const QA_PENDING   = 'Pending';
    public const QA_GOOD      = 'Good';
    public const QA_AVG       = 'Avg';
    public const QA_BAD       = 'Bad';
    public const QA_IN_REVIEW = 'In Review';

    // ═══════════════════════════════════════════════════════════════════
    //  Submission Status  (leads.submission_status column — lowercase)
    // ═══════════════════════════════════════════════════════════════════

    public const SUB_PENDING      = 'pending';
    public const SUB_APPROVED     = 'approved';
    public const SUB_DECLINED     = 'declined';
    public const SUB_UNDERWRITING = 'underwriting';
    public const SUB_CHARGEBACK   = 'chargeback';

    // ═══════════════════════════════════════════════════════════════════
    //  Issuance Status  (leads.issuance_status column — Title Case)
    //  NOTE: 'Incomplete' is retired — use Not Issued flow upstream.
    // ═══════════════════════════════════════════════════════════════════

    public const ISSUANCE_PENDING = 'Pending';
    public const ISSUANCE_ISSUED  = 'Issued';

    // ═══════════════════════════════════════════════════════════════════
    //  Not Issued Dispositions  (leads.not_issued_disposition — snake_case)
    //  Applied at the Pendings Approved stage by Manager.
    //  Retention officer resolves and clears the block.
    // ═══════════════════════════════════════════════════════════════════

    public const NI_EMAIL_MISSING          = 'email_missing';
    public const NI_SSN_MISSING            = 'ssn_missing';
    public const NI_POSTAL_MAIL_MISSING    = 'postal_mail_missing';
    public const NI_BENEFICIARY_INCOMPLETE = 'beneficiary_incomplete';
    public const NI_DOCTOR_INFO_MISSING    = 'doctor_info_missing';
    public const NI_UNDERWRITING_BY_LAW    = 'underwriting_by_law';
    public const NI_CANCELLED_BY_CUSTOMER  = 'cancelled_by_customer';

    /** Human-readable labels for Not Issued dispositions */
    public const NOT_ISSUED_DISPOSITIONS = [
        self::NI_EMAIL_MISSING          => 'Email Missing',
        self::NI_SSN_MISSING            => 'SSN Missing',
        self::NI_POSTAL_MAIL_MISSING    => 'Postal Mail Missing',
        self::NI_BENEFICIARY_INCOMPLETE => 'Beneficiary Incomplete',
        self::NI_DOCTOR_INFO_MISSING    => 'Doctor Info Missing',
        self::NI_UNDERWRITING_BY_LAW    => 'Underwriting by Law',
        self::NI_CANCELLED_BY_CUSTOMER  => 'Cancelled by Customer',
    ];

    // ═══════════════════════════════════════════════════════════════════
    //  FDFP Types  (leads.not_paid_fdfp_type — snake_case)
    //  First Draft First Pay — applied at Pending Draft by Retention.
    //  'manual_action' requires a secondary not_paid_manual_disposition.
    // ═══════════════════════════════════════════════════════════════════

    public const FDFP_UNSTABLE_TO_LOCATE    = 'unstable_to_locate';
    public const FDFP_INSUFFICIENT_FUND     = 'insufficient_fund';
    public const FDFP_UNAUTHORIZED_PAYMENTS = 'unauthorized_payments';
    public const FDFP_MANUAL_ACTION         = 'manual_action';

    /** Human-readable labels for FDFP types */
    public const FDFP_TYPES = [
        self::FDFP_UNSTABLE_TO_LOCATE    => 'Unstable to Locate',
        self::FDFP_INSUFFICIENT_FUND     => 'Insufficient Fund',
        self::FDFP_UNAUTHORIZED_PAYMENTS => 'Unauthorized Payments',
        self::FDFP_MANUAL_ACTION         => 'Manual Action',
    ];

    // ═══════════════════════════════════════════════════════════════════
    //  Policy Died Reasons  (leads.policy_died_reason — snake_case)
    //  Terminal but re-dialable: lead.status reset to 'active'.
    //  No retention action permitted on these.
    // ═══════════════════════════════════════════════════════════════════

    public const PD_CHARGEBACK_FAILED_PAYMENT = 'chargeback_failed_payment';
    public const PD_CHARGEBACK_CANCELLATION   = 'chargeback_cancellation';

    /** Human-readable labels for Policy Died reasons */
    public const POLICY_DIED_REASONS = [
        self::PD_CHARGEBACK_FAILED_PAYMENT => 'Chargeback Failed Payment',
        self::PD_CHARGEBACK_CANCELLATION   => 'Chargeback Cancellation',
    ];

    // ═══════════════════════════════════════════════════════════════════
    //  Retention Status  (leads.retention_status column — lowercase)
    // ═══════════════════════════════════════════════════════════════════

    public const RETENTION_PENDING  = 'pending';
    public const RETENTION_RETAINED = 'retained';
    public const RETENTION_REWRITE  = 'rewrite';

    // ═══════════════════════════════════════════════════════════════════
    //  Bank Verification Status  (leads.bank_verification_status — Title Case)
    // ═══════════════════════════════════════════════════════════════════

    public const BANK_GOOD    = 'Good';
    public const BANK_AVERAGE = 'Average';
    public const BANK_BAD     = 'Bad';

    // ═══════════════════════════════════════════════════════════════════
    //  User / Employee Status  (users.status / employees.status — mixed case)
    // ═══════════════════════════════════════════════════════════════════

    public const USER_ACTIVE     = 'Active';
    public const USER_INACTIVE   = 'inactive';
    public const USER_TERMINATED = 'Terminated';

    // ═══════════════════════════════════════════════════════════════════
    //  MIS Flag  (employees.mis column — string Yes/No)
    // ═══════════════════════════════════════════════════════════════════

    public const MIS_YES = 'Yes';
    public const MIS_NO  = 'No';

    // ═══════════════════════════════════════════════════════════════════
    //  Salary Status  (salary_records.status — lowercase)
    // ═══════════════════════════════════════════════════════════════════

    public const SALARY_DRAFT      = 'draft';
    public const SALARY_CALCULATED = 'calculated';
    public const SALARY_APPROVED   = 'approved';
    public const SALARY_PAID       = 'paid';

    // ═══════════════════════════════════════════════════════════════════
    //  PABS Project Status  (pabs_projects.status — UPPER CASE)
    // ═══════════════════════════════════════════════════════════════════

    public const PABS_DRAFT            = 'DRAFT';
    public const PABS_SCOPING          = 'SCOPING';
    public const PABS_QUOTING          = 'QUOTING';
    public const PABS_PENDING_APPROVAL = 'PENDING APPROVAL';
    public const PABS_BUDGET_ALLOCATED = 'BUDGET ALLOCATED';
    public const PABS_IN_PROGRESS      = 'IN PROGRESS';
    public const PABS_COMPLETED        = 'COMPLETED';
    public const PABS_ARCHIVED         = 'ARCHIVED';
    public const PABS_REJECTED         = 'REJECTED';
    public const PABS_CLARIFICATION    = 'CLARIFICATION NEEDED';

    /** All active PABS project statuses (for scope queries) */
    public const PABS_ACTIVE_STATUSES = [
        self::PABS_SCOPING,
        self::PABS_QUOTING,
        self::PABS_PENDING_APPROVAL,
        self::PABS_BUDGET_ALLOCATED,
        self::PABS_IN_PROGRESS,
    ];

    // ═══════════════════════════════════════════════════════════════════
    //  PABS Ticket Status  (pabs_tickets.status — UPPER CASE)
    // ═══════════════════════════════════════════════════════════════════

    public const TICKET_OPEN        = 'OPEN';
    public const TICKET_IN_PROGRESS = 'IN PROGRESS';
    public const TICKET_ON_HOLD     = 'ON HOLD';
    public const TICKET_RESOLVED    = 'RESOLVED';
    public const TICKET_CLOSED      = 'CLOSED';

    /** Open ticket statuses */
    public const TICKET_OPEN_STATUSES = [
        self::TICKET_OPEN,
        self::TICKET_IN_PROGRESS,
        self::TICKET_ON_HOLD,
    ];

    /** Closed ticket statuses */
    public const TICKET_CLOSED_STATUSES = [
        self::TICKET_RESOLVED,
        self::TICKET_CLOSED,
    ];

    // ═══════════════════════════════════════════════════════════════════
    //  PABS Ticket / Project Approval Status  (approval_status — UPPER CASE)
    // ═══════════════════════════════════════════════════════════════════

    public const APPROVAL_PENDING       = 'PENDING';
    public const APPROVAL_APPROVED      = 'APPROVED';
    public const APPROVAL_REJECTED      = 'REJECTED';
    public const APPROVAL_CLARIFICATION = 'CLARIFICATION NEEDED';

    // ═══════════════════════════════════════════════════════════════════
    //  PABS Priority  (pabs_tickets / pabs_projects priority — UPPER CASE)
    // ═══════════════════════════════════════════════════════════════════

    public const PRIORITY_HIGH   = 'HIGH';
    public const PRIORITY_MEDIUM = 'MEDIUM';
    public const PRIORITY_LOW    = 'LOW';

    // ═══════════════════════════════════════════════════════════════════
    //  EPMS Statuses  (epms_tasks / sprints / projects — lowercase)
    // ═══════════════════════════════════════════════════════════════════

    public const EPMS_PENDING     = 'pending';
    public const EPMS_IN_PROGRESS = 'in-progress';
    public const EPMS_COMPLETED   = 'completed';
    public const EPMS_DONE        = 'done';
    public const EPMS_ACTIVE      = 'active';

    // EPMS Risk / Milestone statuses
    public const EPMS_RISK_RESOLVED = 'resolved';
    public const EPMS_RISK_ACCEPTED = 'accepted';
    public const EPMS_MILESTONE_MISSED = 'missed';

    // ═══════════════════════════════════════════════════════════════════
    //  Dock Status  (dock_records.status — lowercase)
    // ═══════════════════════════════════════════════════════════════════

    public const DOCK_ACTIVE = 'active';

    // ═══════════════════════════════════════════════════════════════════
    //  Attendance Status  (attendance.status — lowercase)
    // ═══════════════════════════════════════════════════════════════════

    public const ATTENDANCE_PRESENT = 'present';
    public const ATTENDANCE_ABSENT  = 'absent';
    public const ATTENDANCE_LEAVE   = 'leave';
    public const ATTENDANCE_LATE    = 'late';
}
