<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Lead;
use App\Support\Statuses;
use App\Support\Teams;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RavensValidationController extends Controller
{
    /**
     * Ravens Validation Dashboard
     * Shows Ravens leads with manager_status = approved or declined
     * that have not yet been reviewed by a validator.
     */
    public function index(Request $request)
    {
        $filter      = $request->get('filter', 'today');
        $customStart = $request->get('start_date');
        $customEnd   = $request->get('end_date');
        $showAll     = $request->boolean('show_all');
        $search      = $request->get('search');

        [$startDate, $endDate] = $this->getDateRange($filter, $customStart, $customEnd);

        // Pending: Ravens sales not yet validated
        $pendingQuery = Lead::where('team', Teams::RAVENS)
            ->whereNull('ravens_validated_at')
            ->whereNotNull('closer_name')
            ->where('cn_name', '!=', '')
            ->whereNotNull('cn_name')
            ->where(function($q) {
                $q->whereNotNull('sale_at')->orWhereNotNull('sale_date');
            });

        if (!$showAll) {
            $pendingQuery->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('manager_reviewed_at', [$startDate, $endDate])
                  ->orWhereBetween('updated_at', [$startDate, $endDate]);
            });
        }

        if ($search) {
            $pendingQuery->where(function ($q) use ($search) {
                $q->where('cn_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('closer_name', 'like', "%{$search}%");
            });
        }

        $pendingLeads = $pendingQuery
            ->orderByDesc('manager_reviewed_at')
            ->orderByDesc('updated_at')
            ->get();

        // Reviewed: validated within the selected date range
        $reviewedLeads = Lead::where('team', Teams::RAVENS)
            ->whereNotNull('ravens_validated_at')
            ->whereBetween('ravens_validated_at', [$startDate, $endDate])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('cn_name', 'like', "%{$search}%")
                          ->orWhere('phone_number', 'like', "%{$search}%")
                          ->orWhere('closer_name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('ravens_validated_at')
            ->get();

        // KPI stats - always against today
        $tz         = 'America/Los_Angeles';
        $appTz      = config('app.timezone', 'UTC');
        $todayStart = Carbon::today($tz)->startOfDay()->setTimezone($appTz);
        $todayEnd   = Carbon::today($tz)->endOfDay()->setTimezone($appTz);

        $todayStats = [
            'pending' => Lead::where('team', Teams::RAVENS)
                ->whereNull('ravens_validated_at')
                ->whereNotNull('closer_name')
                ->where('cn_name', '!=', '')
                ->whereNotNull('cn_name')
                ->where(function($q) {
                    $q->whereNotNull('sale_at')->orWhereNotNull('sale_date');
                })
                ->count(),
            'validated' => Lead::where('team', Teams::RAVENS)
                ->whereNotNull('ravens_validated_at')
                ->whereBetween('ravens_validated_at', [$todayStart, $todayEnd])
                ->count(),
            'sent_to_policy' => Lead::where('team', Teams::RAVENS)
                ->where('ravens_validation_status', 'valid')
                ->whereNotNull('ravens_validated_at')
                ->whereBetween('ravens_validated_at', [$todayStart, $todayEnd])
                ->count(),
            'kept_declined' => Lead::where('team', Teams::RAVENS)
                ->where('ravens_validation_status', 'not_valid')
                ->whereNotNull('ravens_validated_at')
                ->whereBetween('ravens_validated_at', [$todayStart, $todayEnd])
                ->count(),
        ];

        return view('ravens.validation', compact(
            'pendingLeads',
            'reviewedLeads',
            'todayStats',
            'filter',
            'search',
            'showAll'
        ));
    }

    /**
     * Mark lead as valid — stamps ravens_validated_at + ravens_validation_status.
     * The lead will now appear on the Sales page for manager review.
     */
    public function markValid(Request $request, Lead $lead)
    {
        $this->abortIfNotRavens($lead);

        $lead->update([
            'ravens_validated_at'      => now(),
            'ravens_validated_by'      => Auth::user()->name,
            'ravens_validation_status' => 'valid',
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Ravens Validation — Lead Valid',
            'model'      => 'Lead',
            'model_id'   => $lead->id,
            'old_values' => json_encode(['ravens_validation_status' => null]),
            'new_values' => json_encode(['ravens_validation_status' => 'valid']),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('ravens.validation.index')
            ->with('success', "Lead \"{$lead->cn_name}\" marked as valid.");
    }

    /**
     * Mark lead as not valid — sale is flagged as invalid by Ravens validator.
     */
    public function keepDeclined(Request $request, Lead $lead)
    {
        $this->abortIfNotRavens($lead);

        $lead->update([
            'ravens_validated_at'      => now(),
            'ravens_validated_by'      => Auth::user()->name,
            'ravens_validation_status' => 'not_valid',
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Ravens Validation — Lead Not Valid',
            'model'      => 'Lead',
            'model_id'   => $lead->id,
            'old_values' => json_encode(['ravens_validation_status' => null]),
            'new_values' => json_encode(['ravens_validation_status' => 'not_valid']),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('ravens.validation.index')
            ->with('success', "Lead \"{$lead->cn_name}\" marked as not valid.");
    }

    /**
     * Undo validation — resets ravens_validated_at so the lead re-enters the pending queue.
     */
    public function undoValidation(Request $request, Lead $lead)
    {
        $this->abortIfNotRavens($lead);

        $old = $lead->ravens_validated_at;

        $lead->update([
            'ravens_validated_at'      => null,
            'ravens_validated_by'      => null,
            'ravens_validation_status' => null,
        ]);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Ravens Validation — Undo',
            'model'      => 'Lead',
            'model_id'   => $lead->id,
            'old_values' => json_encode(['ravens_validated_at' => $old]),
            'new_values' => json_encode(['ravens_validated_at' => null]),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('ravens.validation.index')
            ->with('success', "Validation for \"{$lead->cn_name}\" has been reset.");
    }

    // ─── Private Helpers ────────────────────────────────────────────────────

    private function abortIfNotRavens(Lead $lead): void
    {
        if ($lead->team !== Teams::RAVENS) {
            abort(403, 'This lead does not belong to the Ravens team.');
        }
    }

    private function getDateRange(string $filter, ?string $customStart, ?string $customEnd): array
    {
        $tz    = 'America/Los_Angeles';
        $appTz = config('app.timezone', 'UTC');

        if ($filter === 'custom' && $customStart && $customEnd) {
            try {
                return [
                    Carbon::parse($customStart, $tz)->startOfDay()->setTimezone($appTz),
                    Carbon::parse($customEnd, $tz)->endOfDay()->setTimezone($appTz),
                ];
            } catch (\Exception $e) {
                // fall through to today
            }
        }

        if ($filter === 'week') {
            return [
                Carbon::now($tz)->startOfWeek()->startOfDay()->setTimezone($appTz),
                Carbon::now($tz)->endOfWeek()->endOfDay()->setTimezone($appTz),
            ];
        }

        if ($filter === 'month') {
            return [
                Carbon::now($tz)->startOfMonth()->startOfDay()->setTimezone($appTz),
                Carbon::now($tz)->endOfMonth()->endOfDay()->setTimezone($appTz),
            ];
        }

        return [
            Carbon::today($tz)->startOfDay()->setTimezone($appTz),
            Carbon::today($tz)->endOfDay()->setTimezone($appTz),
        ];
    }
}
