<?php

namespace App\Services;

use App\Models\Lead;
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
            'submitted_today' => Lead::whereBetween('created_at', [$start, $end])
                ->whereNotNull('verified_by')
                ->count(),
            'submitted_range' => Lead::whereBetween('created_at', [$start, $end])
                ->whereNotNull('verified_by')
                ->count(),
            'submitted_mtd' => Lead::whereBetween('created_at', [$monthStart, Carbon::now()])
                ->whereNotNull('verified_by')
                ->count(),
            'pending_validation' => Lead::whereNotNull('verified_by')
                ->whereNull('validated_by')
                ->where('manager_status', '!=', 'declined')
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
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::today();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now();
        $monthStart = Carbon::now()->startOfMonth();

        return [
            // Pending - sales awaiting QA review (no date filter for current state)
            'pending' => Lead::where(function($query) {
                    $query->where('qa_status', 'Pending')
                          ->orWhere(function($q) {
                              $q->whereNull('qa_status')
                                ->whereNotNull('sale_at');
                          });
                })->whereNotNull('sale_at')->count(),
            'good' => Lead::where('qa_status', 'Good')
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->count(),
            'avg' => Lead::where('qa_status', 'Avg')
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->count(),
            'bad' => Lead::where('qa_status', 'Bad')
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->count(),
            'reviewed_today' => Lead::whereBetween('sale_at', [$start, $end])
                ->whereNotNull('qa_status')
                ->whereNotNull('sale_at')
                ->where('qa_status', '!=', 'Pending')
                ->count(),
            'reviewed_range' => Lead::whereBetween('sale_at', [$start, $end])
                ->whereNotNull('qa_status')
                ->whereNotNull('sale_at')
                ->where('qa_status', '!=', 'Pending')
                ->count(),
            'reviewed_mtd' => Lead::whereBetween('sale_at', [$monthStart, now()])
                ->whereNotNull('qa_status')
                ->whereNotNull('sale_at')
                ->where('qa_status', '!=', 'Pending')
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
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::today();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now();
        $monthStart = Carbon::now()->startOfMonth();

        return [
            'pending' => Lead::where('team', 'peregrine')
                ->where('status', 'closed')
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
            'declined' => Lead::where('manager_status', 'declined')
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

        // Total Assigned - all leads assigned to validators (matching validator dashboard)
        // Uses closed_at to track when closer sent to validator
        $totalAssigned = Lead::where('team', 'peregrine')
            ->whereNotNull('assigned_validator_id')
            ->whereIn('status', ['closed', 'sale', 'declined', 'returned'])
            ->whereBetween('closed_at', [$start, $end])
            ->count();

        // Approved - leads marked as sale
        $approved = Lead::where('team', 'peregrine')
            ->whereNotNull('assigned_validator_id')
            ->where('status', 'sale')
            ->whereBetween('validated_at', [$start, $end])
            ->count();

        // Returned - leads returned to closers
        $returned = Lead::where('team', 'peregrine')
            ->whereNotNull('assigned_validator_id')
            ->where('status', 'returned')
            ->whereBetween('validated_at', [$start, $end])
            ->count();

        // Declined - leads marked as declined
        $declined = Lead::where('team', 'peregrine')
            ->whereNotNull('assigned_validator_id')
            ->where('status', 'declined')
            ->whereBetween('validated_at', [$start, $end])
            ->count();

        // Pending - leads waiting for validation (status = closed) in date range
        $pending = Lead::where('team', 'peregrine')
            ->whereNotNull('assigned_validator_id')
            ->where('status', 'closed')
            ->whereBetween('closed_at', [$start, $end])
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
        $validatorsByRole = \App\Models\User::role(['Peregrine Validator', 'Manager'])
            ->select('id', 'name')
            ->get();

        // Also get users who are currently assigned as validators in leads
        $assignedValidatorIds = Lead::where('team', 'peregrine')
            ->whereNotNull('assigned_validator_id')
            ->distinct()
            ->pluck('assigned_validator_id')
            ->toArray();

        // Merge both sets of validators (role-based and assigned)
        $additionalValidators = \App\Models\User::whereIn('id', $assignedValidatorIds)
            ->whereNotIn('id', $validatorsByRole->pluck('id'))
            ->select('id', 'name')
            ->get();

        $validators = $validatorsByRole->merge($additionalValidators)->sortBy('name');

        $breakdown = [];

        foreach ($validators as $validator) {
            // Total Assigned to this validator - uses closed_at when closer sent to validator
            $totalAssigned = Lead::where('team', 'peregrine')
                ->where('assigned_validator_id', $validator->id)
                ->whereIn('status', ['closed', 'sale', 'declined', 'returned'])
                ->whereBetween('closed_at', [$start, $end])
                ->count();

            // Pending validation (status = closed) in date range
            $pending = Lead::where('team', 'peregrine')
                ->where('assigned_validator_id', $validator->id)
                ->where('status', 'closed')
                ->whereBetween('closed_at', [$start, $end])
                ->count();

            // Approved by this validator
            $approved = Lead::where('team', 'peregrine')
                ->where('assigned_validator_id', $validator->id)
                ->where('status', 'sale')
                ->whereBetween('validated_at', [$start, $end])
                ->count();

            // Returned by this validator
            $returned = Lead::where('team', 'peregrine')
                ->where('assigned_validator_id', $validator->id)
                ->where('status', 'returned')
                ->whereBetween('validated_at', [$start, $end])
                ->count();

            // Declined by this validator
            $declined = Lead::where('team', 'peregrine')
                ->where('assigned_validator_id', $validator->id)
                ->where('status', 'declined')
                ->whereBetween('validated_at', [$start, $end])
                ->count();
            
            // Submitted to Sales Management (with date filter on sale_at)
            $submitted = Lead::where('team', 'peregrine')
                ->where('assigned_validator_id', $validator->id)
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->count();

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
     * Get per-verifier breakdown with individual stats
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getVerifierBreakdown($startDate = null, $endDate = null)
    {
        // Date range is already in UTC from controller
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();

        // Get verifiers who have verified leads
        $verifierIds = Lead::whereNotNull('verified_by')
            ->distinct()
            ->pluck('verified_by')
            ->toArray();

        if (empty($verifierIds)) {
            return collect([]);
        }

        $verifiers = \App\Models\User::whereIn('id', $verifierIds)
            ->select('id', 'name')
            ->get();

        $breakdown = [];

        foreach ($verifiers as $verifier) {
            // Total submitted (all leads verified by this person in date range)
            $totalSubmitted = Lead::where('verified_by', $verifier->id)
                ->whereNotNull('verified_at')
                ->whereBetween('verified_at', [$start, $end])
                ->count();

            // Transferred (leads moved forward to closed/transferred status)
            $transferred = Lead::where('verified_by', $verifier->id)
                ->whereIn('status', ['closed', 'transferred'])
                ->whereNotNull('transferred_at')
                ->whereBetween('verified_at', [$start, $end])
                ->count();

            // Pending callbacks (leads awaiting follow-up) in date range
            $pendingCallbacks = Lead::where('verified_by', $verifier->id)
                ->where('status', 'pending')
                ->whereBetween('verified_at', [$start, $end])
                ->count();

            // Declined calls (leads declined)
            $declinedCalls = Lead::where('verified_by', $verifier->id)
                ->where('status', 'declined')
                ->whereBetween('verified_at', [$start, $end])
                ->count();

            // Marked as sale (leads with sale status approved)
            $markedAsSale = Lead::where('verified_by', $verifier->id)
                ->whereIn('status', ['sale', 'approved'])
                ->whereBetween('verified_at', [$start, $end])
                ->count();

            $breakdown[] = [
                'id' => $verifier->id,
                'name' => $verifier->name,
                'total_submitted' => $totalSubmitted,
                'transferred' => $transferred,
                'pending_callbacks' => $pendingCallbacks,
                'declined_calls' => $declinedCalls,
                'marked_as_sale' => $markedAsSale,
            ];
        }

        return collect($breakdown);
    }

    /**
     * Get per-Peregrine Closer breakdown with individual stats
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

        // Get Peregrine closers who have leads assigned
        $closerIds = Lead::where('team', 'peregrine')
            ->whereNotNull('managed_by')
            ->distinct()
            ->pluck('managed_by')
            ->toArray();

        if (empty($closerIds)) {
            return collect([]);
        }

        $closers = \App\Models\User::whereIn('id', $closerIds)
            ->select('id', 'name')
            ->get();

        $breakdown = [];

        foreach ($closers as $closer) {
            // Total assigned leads (in date range based on transferred_at - when closer received work)
            $totalAssigned = Lead::where('team', 'peregrine')
                ->where('managed_by', $closer->id)
                ->whereBetween('transferred_at', [$start, $end])
                ->count();

            // Pending (in progress) in date range
            $pending = Lead::where('team', 'peregrine')
                ->where('managed_by', $closer->id)
                ->whereIn('status', ['pending', 'transferred'])
                ->whereBetween('transferred_at', [$start, $end])
                ->count();

            // Completed/Closed (sent to validator)
            $closed = Lead::where('team', 'peregrine')
                ->where('managed_by', $closer->id)
                ->where('status', 'closed')
                ->whereBetween('closed_at', [$start, $end])
                ->count();

            // Sales (validator approved as sale)
            $sales = Lead::where('team', 'peregrine')
                ->where('managed_by', $closer->id)
                ->where('status', 'sale')
                ->whereBetween('validated_at', [$start, $end])
                ->count();

            // Returned (sent back by validator)
            $returned = Lead::where('team', 'peregrine')
                ->where('managed_by', $closer->id)
                ->where('status', 'returned')
                ->whereBetween('returned_at', [$start, $end])
                ->count();

            // Declined (rejected by validator or closer)
            $declined = Lead::where('team', 'peregrine')
                ->where('managed_by', $closer->id)
                ->where('status', 'declined')
                ->whereBetween('declined_at', [$start, $end])
                ->count();

            // Conversion rate
            $totalProcessed = $closed + $sales + $returned + $declined;
            $conversionRate = $totalProcessed > 0 ? round(($sales / $totalProcessed) * 100, 1) : 0;

            $breakdown[] = [
                'id' => $closer->id,
                'name' => $closer->name,
                'total_assigned' => $totalAssigned,
                'pending' => $pending,
                'closed' => $closed,
                'sales' => $sales,
                'returned' => $returned,
                'declined' => $declined,
                'total_processed' => $totalProcessed,
                'conversion_rate' => $conversionRate,
            ];
        }

        return collect($breakdown);
    }

    /**
     * Get per-QA reviewer breakdown with individual stats
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getQABreakdown($startDate = null, $endDate = null)
    {
        // Date range is already in UTC from controller
        $start = $startDate ?: Carbon::today()->startOfDay();
        $end = $endDate ?: Carbon::now();

        // Get QA reviewers who have reviewed leads
        $qaReviewerIds = Lead::whereNotNull('qa_user_id')
            ->distinct()
            ->pluck('qa_user_id')
            ->toArray();

        if (empty($qaReviewerIds)) {
            return collect([]);
        }

        $qaReviewers = \App\Models\User::whereIn('id', $qaReviewerIds)
            ->select('id', 'name')
            ->get();

        $breakdown = [];

        foreach ($qaReviewers as $reviewer) {
            // Total sales reviewed - filter by sale_at (when sales come to Sales Management)
            // QA reviews sales that have come from Sales Management (with sale_at timestamp)
            $totalSales = Lead::where('qa_user_id', $reviewer->id)
                ->whereNotNull('qa_status')
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->count();

            // Pending (current state, no date filter) - sales awaiting QA review
            $pending = Lead::where('qa_user_id', $reviewer->id)
                ->where('qa_status', 'Pending')
                ->whereNotNull('sale_at')
                ->count();

            // Good reviews - filter by sale_at
            $good = Lead::where('qa_user_id', $reviewer->id)
                ->where('qa_status', 'Good')
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->count();

            // Issues (Bad + Avg combined as "issues") - filter by sale_at
            $issues = Lead::where('qa_user_id', $reviewer->id)
                ->whereIn('qa_status', ['Bad', 'Avg'])
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->count();

            // Total reviewed (all except pending) - filter by sale_at
            $totalReviewed = Lead::where('qa_user_id', $reviewer->id)
                ->whereNotNull('qa_status')
                ->where('qa_status', '!=', 'Pending')
                ->whereNotNull('sale_at')
                ->whereBetween('sale_at', [$start, $end])
                ->count();

            $breakdown[] = [
                'id' => $reviewer->id,
                'name' => $reviewer->name,
                'total_sales' => $totalSales,
                'pending' => $pending,
                'good' => $good,
                'issues' => $issues,
                'total_reviewed' => $totalReviewed,
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

        $leads = Lead::where('team', 'peregrine')
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
     * Get sales metrics
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getSalesMetrics($startDate = null, $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::today();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now();
        $monthStart = Carbon::now()->startOfMonth();

        return [
            'today' => Lead::whereBetween('sale_at', [$start, $end])->count(),
            'range' => Lead::whereBetween('sale_at', [$start, $end])->count(),
            'mtd' => Lead::whereBetween('sale_at', [$monthStart, now()])->count(),
            'ytd' => Lead::whereYear('sale_at', now()->year)->count(),
            'revenue_range' => Lead::whereBetween('sale_at', [$start, $end])
                ->where('manager_status', 'approved')
                ->where('issuance_status', 'Issued')
                ->sum('monthly_premium'),
            'revenue_mtd' => Lead::whereBetween('sale_at', [$monthStart, now()])
                ->where('manager_status', 'approved')
                ->where('issuance_status', 'Issued')
                ->sum('monthly_premium'),
            'pending_approval' => Lead::whereNotNull('sale_at')
                ->where('manager_status', 'pending')
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
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::today();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now();
        $monthStart = Carbon::now()->startOfMonth();

        return [
            'pending' => Lead::where('manager_status', 'pending')->count(),
            'approved' => Lead::where('manager_status', 'approved')->count(),
            'declined' => Lead::where('manager_status', 'declined')->count(),
            'approved_today' => Lead::whereBetween('updated_at', [$start, $end])
                ->where('manager_status', 'approved')
                ->count(),
            'approved_range' => Lead::whereBetween('updated_at', [$start, $end])
                ->where('manager_status', 'approved')
                ->count(),
            'approved_mtd' => Lead::whereBetween('updated_at', [$monthStart, now()])
                ->where('manager_status', 'approved')
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
                    ->where('qa_status', '!=', 'Pending')
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
                    ->where('qa_status', '!=', 'Pending')
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
                return $query->where('team', 'peregrine')
                    ->where('status', 'closed')
                    ->whereNotNull('assigned_validator_id')
                    ->orderByDesc('created_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'verified_by', 'assigned_validator_id', 'created_at', 'phone_number']);

            case 'qa_pending':
                return $query->where(function($q) {
                        $q->where('qa_status', 'Pending')
                          ->orWhere(function($subq) {
                              $subq->whereNull('qa_status')
                                   ->whereNotNull('sale_at');
                          });
                    })
                    ->orderByDesc('created_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'qa_status', 'created_at', 'phone_number', 'sale_at']);

            case 'qa_good':
                return $query->where('qa_status', 'Good')
                    ->whereNotNull('sale_at')
                    ->whereBetween('sale_at', [$start, $end])
                    ->orderByDesc('sale_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'qa_user_id', 'qa_status', 'sale_at', 'updated_at']);

            case 'qa_bad':
                return $query->where('qa_status', 'Bad')
                    ->whereNotNull('sale_at')
                    ->whereBetween('sale_at', [$start, $end])
                    ->orderByDesc('sale_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'qa_user_id', 'qa_status', 'qa_reason', 'sale_at', 'updated_at']);

            case 'validator_pending':
            case 'validator_total_assigned':
                // Return validator summary with lead counts (only actual validators, not verifiers)
                $validatorIds = Lead::whereNotNull('validated_by')->distinct()->pluck('validated_by')->toArray();
                return Lead::where('leads.team', 'peregrine')
                    ->where('leads.status', 'closed')
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
                return Lead::where('leads.team', 'peregrine')
                    ->whereNotNull('leads.validated_by')
                    ->where('leads.status', 'sale')
                    ->whereBetween('leads.updated_at', [$start, $end])
                    ->join('users', 'leads.validated_by', '=', 'users.id')
                    ->select('users.id as validator_id', 'users.name as validator_name', DB::raw('COUNT(leads.id) as lead_count'))
                    ->groupBy('users.id', 'users.name')
                    ->orderByDesc('lead_count')
                    ->get();

            case 'validator_returned':
                // Return validator summary with lead counts
                return Lead::where('leads.team', 'peregrine')
                    ->whereNotNull('leads.validated_by')
                    ->where('leads.status', 'returned')
                    ->whereBetween('leads.updated_at', [$start, $end])
                    ->join('users', 'leads.validated_by', '=', 'users.id')
                    ->select('users.id as validator_id', 'users.name as validator_name', DB::raw('COUNT(leads.id) as lead_count'))
                    ->groupBy('users.id', 'users.name')
                    ->orderByDesc('lead_count')
                    ->get();

            case 'validator_declined':
                // Return validator summary with lead counts
                return Lead::where('leads.team', 'peregrine')
                    ->whereNotNull('leads.validated_by')
                    ->where('leads.status', 'declined')
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
                return $query->where('manager_status', 'pending')
                    ->whereNotNull('sale_at')
                    ->orderByDesc('sale_at')
                    ->limit($limit)
                    ->get(['id', 'cn_name', 'closer_name', 'sale_at', 'monthly_premium']);

            case 'manager_approved':
                return $query->whereBetween('updated_at', [$start, $end])
                    ->where('manager_status', 'approved')
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
