<?php

namespace App\Services;

use App\Models\Lead;
use App\Support\Roles;
use App\Support\Statuses;
use App\Support\Teams;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get all live analytics metrics for the dashboard
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getLiveMetrics($startDate = null, $endDate = null): array
    {
        return [
            'verifier' => $this->getVerifierMetrics($startDate, $endDate),
            'qa' => $this->getQAMetrics($startDate, $endDate),
            'validator' => $this->getValidatorMetrics($startDate, $endDate),
            'sales' => $this->getSalesMetrics($startDate, $endDate),
            'manager' => $this->getManagerMetrics($startDate, $endDate),
        ];
    }

    /**
     * Get verifier submission metrics
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getVerifierMetrics($startDate = null, $endDate = null): array
    {
        // Date range is already in UTC from controller
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();
        $monthStart = Carbon::now()->startOfMonth();

        return [
            'submitted_today' => Lead::whereBetween('verified_at', [$start, $end])
                ->whereNotNull('verified_by')
                ->count(),
            'submitted_range' => Lead::whereBetween('verified_at', [$start, $end])
                ->whereNotNull('verified_by')
                ->count(),
            'submitted_mtd' => Lead::whereBetween('verified_at', [$monthStart, Carbon::now()])
                ->whereNotNull('verified_by')
                ->count(),
            'pending_validation' => Lead::whereNotNull('verified_by')
                ->whereNull('validated_by')
                ->where('manager_status', '!=', Statuses::MGR_DECLINED)
                ->count(),
            'total_verifiers' => Lead::whereNotNull('verified_by')
                ->distinct('verified_by')
                ->count('verified_by'),
        ];
    }

    /**
     * Get QA review metrics
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getQAMetrics($startDate = null, $endDate = null): array
    {
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();
        $monthStart = Carbon::now()->startOfMonth();

        return [
            // Pending - sales awaiting QA review (current state, not date-filtered)
            'pending' => Lead::where(function($query) {
                    $query->where('qa_status', Statuses::QA_PENDING)
                          ->orWhere(function($q) {
                              $q->whereNull('qa_status')
                                ->whereNotNull('sale_at');
                          });
                })->whereNotNull('sale_at')->count(),
            'good' => Lead::where('qa_status', Statuses::QA_GOOD)
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->count(),
            'avg' => Lead::where('qa_status', Statuses::QA_AVG)
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->count(),
            'bad' => Lead::where('qa_status', Statuses::QA_BAD)
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->count(),
            'reviewed_today' => Lead::whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->whereNotNull('qa_status')
                ->where('qa_status', '!=', Statuses::QA_PENDING)
                ->count(),
            'reviewed_range' => Lead::whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->whereNotNull('qa_status')
                ->where('qa_status', '!=', Statuses::QA_PENDING)
                ->count(),
            'reviewed_mtd' => Lead::whereNotNull('sale_at')
                ->whereBetween('sale_at', [$monthStart, Carbon::now()])
                ->whereNotNull('qa_status')
                ->where('qa_status', '!=', Statuses::QA_PENDING)
                ->count(),
        ];
    }

    /**
     * Get validator metrics
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getValidatorMetrics($startDate = null, $endDate = null): array
    {
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();
        $monthStart = Carbon::now()->startOfMonth();

        return [
            'pending' => Lead::where('team', Teams::PEREGRINE)
                ->where('status', Statuses::LEAD_CLOSED)
                ->whereNotNull('assigned_validator_id')
                ->count(),
            'submitted_today' => Lead::whereBetween('updated_at', [$start, $end])
                ->whereNotNull('validated_by')
                ->count(),
            'submitted_range' => Lead::whereBetween('updated_at', [$start, $end])
                ->whereNotNull('validated_by')
                ->count(),
            'submitted_mtd' => Lead::whereBetween('updated_at', [$monthStart, now()])
                ->whereNotNull('validated_by')
                ->count(),
            'declined' => Lead::where('manager_status', Statuses::MGR_DECLINED)
                ->whereNotNull('validated_by')
                ->count(),
        ];
    }

    /**
     * Get validator form metrics (aggregate counts for all validators)
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getValidatorFormMetrics($startDate = null, $endDate = null): array
    {
        // Date range is already in UTC from controller
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();

        $base = Lead::where('team', Teams::PEREGRINE)
            ->whereNotNull('assigned_validator_id')
            ->whereBetween('verified_at', [$start, $end]);

        // Total Assigned - leads assigned to validators in date range
        $totalAssigned = (clone $base)
            ->whereIn('status', [Statuses::LEAD_CLOSED, Statuses::LEAD_SALE, Statuses::LEAD_DECLINED, Statuses::LEAD_RETURNED])
            ->count();

        // Approved - leads marked as sale
        $approved = (clone $base)
            ->where('status', Statuses::LEAD_SALE)
            ->count();

        // Returned - leads returned to closers
        $returned = (clone $base)
            ->where('status', Statuses::LEAD_RETURNED)
            ->count();

        // Declined - leads marked as declined
        $declined = (clone $base)
            ->where('status', Statuses::LEAD_DECLINED)
            ->count();

        // Pending - leads waiting for validation (status = closed)
        $pending = (clone $base)
            ->where('status', Statuses::LEAD_CLOSED)
            ->count();

        return [
            'total_assigned' => $totalAssigned,
            'pending' => $pending,
            'approved' => $approved,
            'returned' => $returned,
            'declined' => $declined,
        ];
    }

    /**
     * Get per-validator breakdown with individual stats
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getValidatorBreakdown($startDate = null, $endDate = null)
    {
        // Date range is already in UTC from controller
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();

        // Get validators by role (Managers and Peregrine Validators) OR users currently assigned as validators
        $validatorsByRole = \App\Models\User::role([Roles::PEREGRINE_VALIDATOR, Roles::MANAGER])
            ->select('id', 'name')
            ->get();

        // Also get users who are currently assigned as validators in leads within date range
        $assignedValidatorIds = Lead::where('team', Teams::PEREGRINE)
            ->whereNotNull('assigned_validator_id')
            ->whereBetween('verified_at', [$start, $end])
            ->distinct()
            ->pluck('assigned_validator_id')
            ->toArray();

        // Merge both sets of validators (role-based and assigned)
        $additionalValidators = \App\Models\User::withTrashed()->whereIn('id', $assignedValidatorIds)
            ->whereNotIn('id', $validatorsByRole->pluck('id'))
            ->select('id', 'name')
            ->get();

        $validators = $validatorsByRole->merge($additionalValidators)->sortBy('name');

        $breakdown = [];

        foreach ($validators as $validator) {
            // Base query: Peregrine leads assigned to this validator in date range
            $base = Lead::where('team', Teams::PEREGRINE)
                ->where('assigned_validator_id', $validator->id)
                ->whereBetween('verified_at', [$start, $end]);

            // Total Assigned to this validator
            $totalAssigned = (clone $base)
                ->whereIn('status', [Statuses::LEAD_CLOSED, Statuses::LEAD_SALE, Statuses::LEAD_DECLINED, Statuses::LEAD_RETURNED])
                ->count();

            // Pending validation (status = closed)
            $pending = (clone $base)
                ->where('status', Statuses::LEAD_CLOSED)
                ->count();

            // Approved by this validator (sale)
            $approved = (clone $base)
                ->where('status', Statuses::LEAD_SALE)
                ->count();

            // Returned by this validator
            $returned = (clone $base)
                ->where('status', Statuses::LEAD_RETURNED)
                ->count();

            // Declined by this validator
            $declined = (clone $base)
                ->where('status', Statuses::LEAD_DECLINED)
                ->count();
            
            // Submitted to Sales Management (with sale_at)
            $submitted = (clone $base)
                ->whereNotNull('sale_at')
                ->count();

            // Skip validators with no activity
            if ($totalAssigned == 0) {
                continue;
            }

            $breakdown[] = [
                'id' => $validator->id,
                'name' => $validator->name,
                'total_assigned' => $totalAssigned,
                'pending' => $pending,
                'submitted' => $submitted,
                'approved' => $approved,
                'returned' => $returned,
                'declined' => $declined,
                'total_processed' => $approved + $returned + $declined,
            ];
        }

        return collect($breakdown);
    }

    /**
     * Get per-verifier pipeline breakdown showing the full funnel
     * Total Submitted → Disposed (Bad) → Pending → Sales → Declined
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getVerifierPipelineBreakdown($startDate = null, $endDate = null)
    {
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();

        // Get verifiers who have verified Peregrine leads in date range
        $verifierIds = Lead::where('team', Teams::PEREGRINE)
            ->whereNotNull('verified_by')
            ->whereBetween('verified_at', [$start, $end])
            ->distinct()
            ->pluck('verified_by')
            ->toArray();

        if (empty($verifierIds)) {
            return collect([]);
        }

        $verifiers = \App\Models\User::withTrashed()->whereIn('id', $verifierIds)
            ->select('id', 'name')
            ->get();

        $breakdown = [];

        foreach ($verifiers as $verifier) {
            $base = Lead::where('team', Teams::PEREGRINE)
                ->where('verified_by', $verifier->id)
                ->whereBetween('verified_at', [$start, $end]);

            $total = (clone $base)->count();

            // Disposed (Bad) = closer disposed as bad (status=declined, no sale)
            $disposed = (clone $base)
                ->where('status', Statuses::LEAD_DECLINED)
                ->whereNull('sale_at')
                ->count();

            // Pending = still in pipeline (not disposed, not a sale, not manager-declined)
            $pending = (clone $base)
                ->whereIn('status', [Statuses::LEAD_PENDING, Statuses::LEAD_TRANSFERRED, Statuses::LEAD_CLOSED, Statuses::LEAD_ACTIVE, Statuses::LEAD_FORWARDED])
                ->count();

            // Sales = leads that resulted in a sale
            $sales = (clone $base)
                ->whereIn('status', [Statuses::LEAD_SALE, Statuses::LEAD_ACCEPTED])
                ->count();

            // Declined = manager declined the sale
            $declined = (clone $base)
                ->where('manager_status', Statuses::MGR_DECLINED)
                ->count();

            if ($total == 0) {
                continue;
            }

            $breakdown[] = [
                'id' => $verifier->id,
                'name' => $verifier->name,
                'total' => $total,
                'disposed' => $disposed,
                'pending' => $pending,
                'sales' => $sales,
                'declined' => $declined,
            ];
        }

        return collect($breakdown);
    }

    /**
     * Get verifier submission log - individual form submissions with date/time
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getVerifierSubmissionLog($startDate = null, $endDate = null)
    {
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();

        $leads = Lead::where('team', Teams::PEREGRINE)
            ->whereNotNull('verified_by')
            ->whereBetween('verified_at', [$start, $end])
            ->orderBy('verified_at', 'desc')
            ->limit(100)
            ->get(['id', 'cn_name', 'phone_number', 'verified_by', 'managed_by', 'status', 'verified_at', 'decline_reason', 'sale_at']);

        // Collect unique user IDs for verifiers and closers
        $userIds = $leads->pluck('verified_by')
            ->merge($leads->pluck('managed_by'))
            ->filter()
            ->unique()
            ->toArray();

        $users = \App\Models\User::withTrashed()
            ->whereIn('id', $userIds)
            ->pluck('name', 'id');

        return $leads->map(function ($lead) use ($users) {
            // Determine a friendly status label
            $statusLabel = ucfirst($lead->status ?? 'unknown');
            if ($lead->status === Statuses::LEAD_TRANSFERRED) {
                $statusLabel = 'With Closer';
            } elseif ($lead->status === Statuses::LEAD_CLOSED) {
                $statusLabel = 'With Validator';
            } elseif ($lead->status === Statuses::LEAD_SALE || $lead->status === Statuses::LEAD_ACCEPTED) {
                $statusLabel = 'Sale';
            } elseif ($lead->status === Statuses::LEAD_DECLINED) {
                $statusLabel = $lead->decline_reason ? 'Declined: ' . str_replace('Failed:', '', $lead->decline_reason) : 'Declined';
            } elseif ($lead->status === Statuses::LEAD_PENDING) {
                $statusLabel = 'Pending';
            } elseif ($lead->status === Statuses::LEAD_RETURNED) {
                $statusLabel = 'Returned';
            }

            return [
                'id' => $lead->id,
                'cn_name' => $lead->cn_name ?? '—',
                'phone' => $lead->phone_number ?? '—',
                'verifier' => $users[$lead->verified_by] ?? '—',
                'closer' => $users[$lead->managed_by] ?? '—',
                'status' => $statusLabel,
                'status_raw' => $lead->status,
                'submitted_at' => $lead->verified_at
                    ? $lead->verified_at->setTimezone('America/Denver')->format('M d, h:i A')
                    : '—',
            ];
        })->values();
    }

    /**
     * Get per-Peregrine Closer breakdown with individual stats
     * Total | Disposed | Callbacks | → Validator | Sales | Declined
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getPeregrineCloserBreakdown($startDate = null, $endDate = null)
    {
        // Date range is already in UTC from controller
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();

        // Get Peregrine closers who have leads assigned in date range
        $closerIds = Lead::where('team', Teams::PEREGRINE)
            ->whereNotNull('managed_by')
            ->whereBetween('verified_at', [$start, $end])
            ->distinct()
            ->pluck('managed_by')
            ->toArray();

        if (empty($closerIds)) {
            return collect([]);
        }

        $closers = \App\Models\User::withTrashed()->whereIn('id', $closerIds)
            ->select('id', 'name')
            ->get();

        $breakdown = [];

        foreach ($closers as $closer) {
            // Base query: Peregrine leads managed by this closer, verified in date range
            $base = Lead::where('team', Teams::PEREGRINE)
                ->where('managed_by', $closer->id)
                ->whereBetween('verified_at', [$start, $end]);

            $totalAssigned = (clone $base)->count();

            // Disposed = closer disposed as bad (status=declined, no sale)
            $disposed = (clone $base)
                ->where('status', Statuses::LEAD_DECLINED)
                ->whereNull('sale_at')
                ->count();

            // Callbacks = pending leads (status=pending)
            $callbacks = (clone $base)
                ->where('status', Statuses::LEAD_PENDING)
                ->count();

            // Sent to Validator (assigned_validator_id set, pending validation)
            $sentToValidator = (clone $base)
                ->whereNotNull('assigned_validator_id')
                ->count();

            // Sales (status=sale or accepted — active sales)
            $sales = (clone $base)
                ->whereIn('status', [Statuses::LEAD_SALE, Statuses::LEAD_ACCEPTED])
                ->count();

            // Declined = manager declined
            $declined = (clone $base)
                ->where('manager_status', Statuses::MGR_DECLINED)
                ->count();

            // Skip closers with no activity
            if ($totalAssigned == 0) {
                continue;
            }

            $breakdown[] = [
                'id' => $closer->id,
                'name' => $closer->name,
                'total_assigned' => $totalAssigned,
                'disposed' => $disposed,
                'callbacks' => $callbacks,
                'sent_to_validator' => $sentToValidator,
                'sales' => $sales,
                'declined' => $declined,
            ];
        }

        return collect($breakdown);
    }

    /**
     * Get per-QA reviewer breakdown with individual stats
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $team  Optional team filter ('peregrine' or 'ravens')
     * @return \Illuminate\Support\Collection
     */
    public function getQABreakdown($startDate = null, $endDate = null, $team = null)
    {
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();

        // Get QA reviewers who have reviewed leads with sales in date range
        $qaQuery = Lead::whereNotNull('qa_user_id')
            ->whereBetween('sale_at', [$start, $end]);
        if ($team) {
            $qaQuery->where('team', $team);
        }
        $qaReviewerIds = $qaQuery->distinct()
            ->pluck('qa_user_id')
            ->toArray();

        if (empty($qaReviewerIds)) {
            return collect([]);
        }

        $qaReviewers = \App\Models\User::withTrashed()->whereIn('id', $qaReviewerIds)
            ->select('id', 'name')
            ->get();

        $breakdown = [];

        foreach ($qaReviewers as $reviewer) {
            // Base query: sales assigned to this QA reviewer within date range
            $base = Lead::where('qa_user_id', $reviewer->id)
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end]);
            if ($team) {
                $base->where('team', $team);
            }

            $totalSales = (clone $base)->count();

            // Pending (current state) - sales awaiting QA review
            $pending = (clone $base)
                ->where('qa_status', Statuses::QA_PENDING)
                ->count();

            // Good reviews
            $good = (clone $base)
                ->where('qa_status', Statuses::QA_GOOD)
                ->count();

            // Avg reviews
            $avg = (clone $base)
                ->where('qa_status', Statuses::QA_AVG)
                ->count();

            // Bad reviews
            $bad = (clone $base)
                ->where('qa_status', Statuses::QA_BAD)
                ->count();

            $breakdown[] = [
                'id' => $reviewer->id,
                'name' => $reviewer->name,
                'total_sales' => $totalSales,
                'pending' => $pending,
                'good' => $good,
                'avg' => $avg,
                'bad' => $bad,
            ];
        }

        return collect($breakdown);
    }

    /**
     * Get per-Ravens Closer breakdown with individual stats
     * Sales first, then manager status breakdown: Pending | Approved | Declined | UW | CB
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getRavensCloserBreakdown($startDate = null, $endDate = null)
    {
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();

        // Ravens closers may not have managed_by set — use closer_name as fallback
        // First get closers who have managed_by set in date range
        $closerIds = Lead::where('team', Teams::RAVENS)
            ->whereNotNull('managed_by')
            ->whereBetween('created_at', [$start, $end])
            ->distinct()
            ->pluck('managed_by')
            ->toArray();

        $breakdown = [];

        if (!empty($closerIds)) {
            $closers = \App\Models\User::withTrashed()->whereIn('id', $closerIds)
                ->select('id', 'name')
                ->get();

            foreach ($closers as $closer) {
                $base = Lead::where('team', Teams::RAVENS)
                    ->where('managed_by', $closer->id)
                    ->whereBetween('created_at', [$start, $end]);

                // Total sales by this closer
                $sales = (clone $base)
                    ->whereNotNull('sale_at')
                    ->count();

                // Manager status breakdown of their sales
                $salesBase = Lead::where('team', Teams::RAVENS)
                    ->where('managed_by', $closer->id)
                    ->whereNotNull('sale_at')
                    ->whereBetween('created_at', [$start, $end]);

                $mgrPending = (clone $salesBase)
                    ->where('manager_status', Statuses::MGR_PENDING)
                    ->count();

                $mgrApproved = (clone $salesBase)
                    ->where('manager_status', Statuses::MGR_APPROVED)
                    ->count();

                $mgrDeclined = (clone $salesBase)
                    ->where('manager_status', Statuses::MGR_DECLINED)
                    ->count();

                $mgrUnderwriting = (clone $salesBase)
                    ->where('manager_status', Statuses::MGR_UNDERWRITING)
                    ->count();

                $mgrChargeback = (clone $salesBase)
                    ->where('manager_status', Statuses::MGR_CHARGEBACK)
                    ->count();

                if ($sales == 0) {
                    continue;
                }

                $breakdown[] = [
                    'name' => $closer->name,
                    'sales' => $sales,
                    'mgr_pending' => $mgrPending,
                    'mgr_approved' => $mgrApproved,
                    'mgr_declined' => $mgrDeclined,
                    'mgr_underwriting' => $mgrUnderwriting,
                    'mgr_chargeback' => $mgrChargeback,
                ];
            }
        }

        // Also get Ravens sales with only closer_name (no managed_by) in date range
        $ravensNoManaged = Lead::where('team', Teams::RAVENS)
            ->whereNull('managed_by')
            ->whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->whereBetween('sale_at', [$start, $end])
            ->select(
                'closer_name',
                DB::raw('count(*) as sales_count'),
                DB::raw("SUM(CASE WHEN manager_status = '" . Statuses::MGR_PENDING . "' THEN 1 ELSE 0 END) as mgr_pending"),
                DB::raw("SUM(CASE WHEN manager_status = '" . Statuses::MGR_APPROVED . "' THEN 1 ELSE 0 END) as mgr_approved"),
                DB::raw("SUM(CASE WHEN manager_status = '" . Statuses::MGR_DECLINED . "' THEN 1 ELSE 0 END) as mgr_declined"),
                DB::raw("SUM(CASE WHEN manager_status = '" . Statuses::MGR_UNDERWRITING . "' THEN 1 ELSE 0 END) as mgr_underwriting"),
                DB::raw("SUM(CASE WHEN manager_status = '" . Statuses::MGR_CHARGEBACK . "' THEN 1 ELSE 0 END) as mgr_chargeback")
            )
            ->groupBy('closer_name')
            ->get();

        foreach ($ravensNoManaged as $row) {
            // Check if this closer_name already exists in breakdown
            $exists = collect($breakdown)->firstWhere('name', $row->closer_name);
            if ($exists) {
                $key = collect($breakdown)->search(fn($b) => $b['name'] === $row->closer_name);
                $breakdown[$key]['sales'] += $row->sales_count;
                $breakdown[$key]['mgr_pending'] += $row->mgr_pending;
                $breakdown[$key]['mgr_approved'] += $row->mgr_approved;
                $breakdown[$key]['mgr_declined'] += $row->mgr_declined;
                $breakdown[$key]['mgr_underwriting'] += $row->mgr_underwriting;
                $breakdown[$key]['mgr_chargeback'] += $row->mgr_chargeback;
            } else {
                $breakdown[] = [
                    'name' => $row->closer_name,
                    'sales' => $row->sales_count,
                    'mgr_pending' => $row->mgr_pending,
                    'mgr_approved' => $row->mgr_approved,
                    'mgr_declined' => $row->mgr_declined,
                    'mgr_underwriting' => $row->mgr_underwriting,
                    'mgr_chargeback' => $row->mgr_chargeback,
                ];
            }
        }

        return collect($breakdown);
    }

    /**
     * Get per-Manager breakdown with individual stats
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $team  Optional team filter ('peregrine' or 'ravens')
     * @return \Illuminate\Support\Collection
     */
    public function getManagerApprovalBreakdown($startDate = null, $endDate = null, $team = null)
    {
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();

        // Get manager users who have approved/declined leads in date range
        $mgrQuery = Lead::whereNotNull('manager_user_id')
            ->whereNotNull('sale_at')
            ->whereBetween('sale_at', [$start, $end]);
        if ($team) {
            $mgrQuery->where('team', $team);
        }
        $managerIds = $mgrQuery->distinct()
            ->pluck('manager_user_id')
            ->toArray();

        // Also get users with Manager role
        $roleManagers = \App\Models\User::role([Roles::MANAGER, Roles::SUPER_ADMIN])
            ->select('id', 'name')
            ->get();

        // Merge sets
        $additionalManagers = \App\Models\User::withTrashed()->whereIn('id', $managerIds)
            ->whereNotIn('id', $roleManagers->pluck('id'))
            ->select('id', 'name')
            ->get();

        $managers = $roleManagers->merge($additionalManagers)->sortBy('name');

        $breakdown = [];

        foreach ($managers as $manager) {
            // Base query: leads with sale in date range that this manager reviewed
            $base = Lead::where('manager_user_id', $manager->id)
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end]);
            if ($team) {
                $base->where('team', $team);
            }

            // Total leads this manager has reviewed in date range
            $totalReviewed = (clone $base)
                ->where('manager_status', '!=', Statuses::MGR_PENDING)
                ->count();

            $approved = (clone $base)
                ->where('manager_status', Statuses::MGR_APPROVED)
                ->count();

            $declined = (clone $base)
                ->where('manager_status', Statuses::MGR_DECLINED)
                ->count();

            $underwriting = (clone $base)
                ->where('manager_status', Statuses::MGR_UNDERWRITING)
                ->count();

            $chargeback = (clone $base)
                ->where('manager_status', Statuses::MGR_CHARGEBACK)
                ->count();

            // Skip managers with zero activity
            if ($totalReviewed == 0) {
                continue;
            }

            $breakdown[] = [
                'id' => $manager->id,
                'name' => $manager->name,
                'total_reviewed' => $totalReviewed,
                'approved' => $approved,
                'declined' => $declined,
                'underwriting' => $underwriting,
                'chargeback' => $chargeback,
            ];
        }

        return collect($breakdown);
    }

    /**
     * Get detailed breakdown of leads for a specific validator
     *
     * @param int $validatorId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getValidatorDetailedBreakdown($validatorId, $startDate = null, $endDate = null)
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::today();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now();

        $leads = Lead::where('team', Teams::PEREGRINE)
            ->where(function($query) use ($validatorId) {
                $query->where('validated_by', $validatorId)
                      ->orWhere('assigned_validator_id', $validatorId);
            })
            ->whereBetween('updated_at', [$start, $end])
            ->with(['assignedCloser:id,name'])
            ->orderByDesc('updated_at')
            ->limit(100)
            ->get(['id', 'first_name', 'last_name', 'phone_number', 'status', 'validated_by', 'assigned_validator_id', 'updated_at', 'managed_by']);

        return $leads;
    }

    /**
     * Get sales metrics with team splits
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getSalesMetrics($startDate = null, $endDate = null): array
    {
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();
        $monthStart = Carbon::now()->startOfMonth();

        return [
            'today' => Lead::whereBetween('sale_at', [$start, $end])->count(),
            'range' => Lead::whereBetween('sale_at', [$start, $end])->count(),
            'peregrine_sales' => Lead::where('team', Teams::PEREGRINE)->whereBetween('sale_at', [$start, $end])->count(),
            'ravens_sales' => Lead::where('team', Teams::RAVENS)->whereBetween('sale_at', [$start, $end])->count(),
            'mtd' => Lead::whereBetween('sale_at', [$monthStart, now()])->count(),
            'ytd' => Lead::whereYear('sale_at', now()->year)->count(),
            'revenue_range' => Lead::whereBetween('sale_at', [$start, $end])
                ->where('manager_status', Statuses::MGR_APPROVED)
                ->where('issuance_status', Statuses::ISSUANCE_ISSUED)
                ->sum('monthly_premium'),
            'revenue_mtd' => Lead::whereBetween('sale_at', [$monthStart, now()])
                ->where('manager_status', Statuses::MGR_APPROVED)
                ->where('issuance_status', Statuses::ISSUANCE_ISSUED)
                ->sum('monthly_premium'),
            'pending_approval' => Lead::whereNotNull('sale_at')
                ->where('manager_status', Statuses::MGR_PENDING)
                ->count(),
        ];
    }

    /**
     * Get manager approval metrics
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getManagerMetrics($startDate = null, $endDate = null): array
    {
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();
        $monthStart = Carbon::now()->startOfMonth();

        return [
            // Pending is current state - not date-filtered
            'pending' => Lead::where('manager_status', Statuses::MGR_PENDING)
                ->whereNotNull('sale_at')->count(),
            // Approved/Declined scoped to sales in date range
            'approved' => Lead::where('manager_status', Statuses::MGR_APPROVED)
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->count(),
            'declined' => Lead::where('manager_status', Statuses::MGR_DECLINED)
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->count(),
            'approved_today' => Lead::whereBetween('sale_at', [$start, $end])
                ->where('manager_status', Statuses::MGR_APPROVED)
                ->count(),
            'approved_range' => Lead::whereBetween('sale_at', [$start, $end])
                ->where('manager_status', Statuses::MGR_APPROVED)
                ->count(),
            'approved_mtd' => Lead::whereBetween('sale_at', [$monthStart, now()])
                ->where('manager_status', Statuses::MGR_APPROVED)
                ->count(),
        ];
    }

    /**
     * Get historical trend data for charts
     *
     * @param int $days Default days to look back if no custom range provided
     * @param string|null $startDate Custom start date
     * @param string|null $endDate Custom end date
     * @return array
     */
    public function getHistoricalTrends(int $days = 30, $startDate = null, $endDate = null): array
    {
        $dates = [];
        $salesData = [];
        $verifierData = [];
        $qaReviewData = [];
        $validatorData = [];

        // If custom date range is provided, use it
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $daysDiff = $start->diffInDays($end);
            
            // Generate date range for custom period
            for ($i = 0; $i <= $daysDiff; $i++) {
                $date = $start->copy()->addDays($i);
                $dates[] = $date->format('M d');

                // Sales count for each day
                $salesData[] = Lead::whereDate('sale_at', $date)->count();

                // Verifier submissions for each day
                $verifierData[] = Lead::whereDate('created_at', $date)
                    ->whereNotNull('verified_by')
                    ->count();

                // QA reviews for each day - filter by sale_at (when sales come to Sales Management)
                $qaReviewData[] = Lead::whereDate('sale_at', $date)
                    ->whereNotNull('qa_status')
                    ->whereNotNull('sale_at')
                    ->where('qa_status', '!=', Statuses::QA_PENDING)
                    ->count();

                // Validator submissions for each day
                $validatorData[] = Lead::whereDate('updated_at', $date)
                    ->whereNotNull('validated_by')
                    ->count();
            }
        } else {
            // Default: use last N days
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->startOfDay();
                $dates[] = $date->format('M d');

                // Sales count for each day
                $salesData[] = Lead::whereDate('sale_at', $date)->count();

                // Verifier submissions for each day
                $verifierData[] = Lead::whereDate('created_at', $date)
                    ->whereNotNull('verified_by')
                    ->count();

                // QA reviews for each day - filter by sale_at (when sales come to Sales Management)
                $qaReviewData[] = Lead::whereDate('sale_at', $date)
                    ->whereNotNull('qa_status')
                    ->whereNotNull('sale_at')
                    ->where('qa_status', '!=', Statuses::QA_PENDING)
                    ->count();

                // Validator submissions for each day
                $validatorData[] = Lead::whereDate('updated_at', $date)
                    ->whereNotNull('validated_by')
                    ->count();
            }
        }

        return [
            'labels' => $dates,
            'sales' => $salesData,
            'verifier' => $verifierData,
            'qa_reviews' => $qaReviewData,
            'validator' => $validatorData,
        ];
    }

    /**
     * Get detailed drill-down data for a specific metric
     *
     * @param string $type
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getDrillDownData(string $type, $startDate = null, $endDate = null, int $limit = 50)
    {
        // Date range is already in UTC from controller
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();

        $query = Lead::with(['verifier:id,name', 'validator:id,name', 'qaUser:id,name', 'managerUser:id,name']);

        switch ($type) {
            case 'verifier_submitted':
                return $query->whereBetween('created_at', [$start, $end])
                    ->whereNotNull('verified_by')
                    ->orderByDesc('created_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'verified_by', 'created_at', 'phone_number']);

            case 'verifier_pending':
                return $query->where('team', Teams::PEREGRINE)
                    ->where('status', Statuses::LEAD_CLOSED)
                    ->whereNotNull('assigned_validator_id')
                    ->orderByDesc('created_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'verified_by', 'assigned_validator_id', 'created_at', 'phone_number']);

            case 'qa_pending':
                return $query->where(function($q) {
                        $q->where('qa_status', Statuses::QA_PENDING)
                          ->orWhere(function($subq) {
                              $subq->whereNull('qa_status')
                                   ->whereNotNull('sale_at');
                          });
                    })
                    ->orderByDesc('created_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'qa_status', 'created_at', 'phone_number', 'sale_at']);

            case 'qa_good':
                return $query->where('qa_status', Statuses::QA_GOOD)
                    ->whereNotNull('sale_at')
                    ->whereBetween('sale_at', [$start, $end])
                    ->orderByDesc('sale_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'qa_user_id', 'qa_status', 'sale_at', 'updated_at']);

            case 'qa_bad':
                return $query->where('qa_status', Statuses::QA_BAD)
                    ->whereNotNull('sale_at')
                    ->whereBetween('sale_at', [$start, $end])
                    ->orderByDesc('sale_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'qa_user_id', 'qa_status', 'qa_reason', 'sale_at', 'updated_at']);

            case 'validator_pending':
            case 'validator_total_assigned':
                // Return validator summary with lead counts (only actual validators, not verifiers)
                $validatorIds = Lead::whereNotNull('validated_by')->distinct()->pluck('validated_by')->toArray();
                return Lead::where('leads.team', Teams::PEREGRINE)
                    ->where('leads.status', Statuses::LEAD_CLOSED)
                    ->whereNotNull('leads.assigned_validator_id')
                    ->whereIn('leads.assigned_validator_id', $validatorIds) // Filter to only actual validators
                    ->whereBetween('leads.created_at', [$start, $end])
                    ->join('users', 'leads.assigned_validator_id', '=', 'users.id')
                    ->select('users.id as validator_id', 'users.name as validator_name', DB::raw('COUNT(leads.id) as lead_count'))
                    ->groupBy('users.id', 'users.name')
                    ->orderByDesc('lead_count')
                    ->get();

            case 'validator_submitted':
                return $query->whereBetween('updated_at', [$start, $end])
                    ->whereNotNull('validated_by')
                    ->orderByDesc('updated_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'validated_by', 'updated_at']);

            case 'validator_approved':
                // Return validator summary with lead counts
                return Lead::where('leads.team', Teams::PEREGRINE)
                    ->whereNotNull('leads.validated_by')
                    ->where('leads.status', Statuses::LEAD_SALE)
                    ->whereBetween('leads.updated_at', [$start, $end])
                    ->join('users', 'leads.validated_by', '=', 'users.id')
                    ->select('users.id as validator_id', 'users.name as validator_name', DB::raw('COUNT(leads.id) as lead_count'))
                    ->groupBy('users.id', 'users.name')
                    ->orderByDesc('lead_count')
                    ->get();

            case 'validator_returned':
                // Return validator summary with lead counts
                return Lead::where('leads.team', Teams::PEREGRINE)
                    ->whereNotNull('leads.validated_by')
                    ->where('leads.status', Statuses::LEAD_RETURNED)
                    ->whereBetween('leads.updated_at', [$start, $end])
                    ->join('users', 'leads.validated_by', '=', 'users.id')
                    ->select('users.id as validator_id', 'users.name as validator_name', DB::raw('COUNT(leads.id) as lead_count'))
                    ->groupBy('users.id', 'users.name')
                    ->orderByDesc('lead_count')
                    ->get();

            case 'validator_declined':
                // Return validator summary with lead counts
                return Lead::where('leads.team', Teams::PEREGRINE)
                    ->whereNotNull('leads.validated_by')
                    ->where('leads.status', Statuses::LEAD_DECLINED)
                    ->whereBetween('leads.updated_at', [$start, $end])
                    ->join('users', 'leads.validated_by', '=', 'users.id')
                    ->select('users.id as validator_id', 'users.name as validator_name', DB::raw('COUNT(leads.id) as lead_count'))
                    ->groupBy('users.id', 'users.name')
                    ->orderByDesc('lead_count')
                    ->get();

            case 'sales_today':
            case 'sales_range':
                return $query->whereBetween('sale_at', [$start, $end])
                    ->orderByDesc('sale_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'closer_name', 'sale_at', 'monthly_premium', 'manager_status']);

            case 'manager_pending':
                return $query->where('manager_status', Statuses::MGR_PENDING)
                    ->whereNotNull('sale_at')
                    ->orderByDesc('sale_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'closer_name', 'sale_at', 'monthly_premium']);

            case 'manager_approved':
                return $query->whereBetween('updated_at', [$start, $end])
                    ->where('manager_status', Statuses::MGR_APPROVED)
                    ->orderByDesc('updated_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'manager_user_id', 'updated_at', 'monthly_premium']);

            default:
                return collect([]);
        }
    }

    /**
     * Get top performers for the current month
     *
     * @param int $limit
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getTopPerformers(int $limit = 5, $startDate = null, $endDate = null)
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now();

        return Lead::select('closer_name', DB::raw('COUNT(*) as sales_count'))
            ->whereNotNull('closer_name')
            ->whereBetween('sale_at', [$start, $end])
            ->groupBy('closer_name')
            ->orderByDesc('sales_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent verifier activity
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getRecentVerifierActivity(int $limit = 10)
    {
        return Lead::with(['verifier:id,name'])
            ->whereNotNull('verified_by')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get(['id', 'cn_name', 'verified_by', 'created_at']);
    }
}
