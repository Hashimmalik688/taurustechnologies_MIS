<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\BadLead;
use App\Models\CallLog;
use App\Models\Attendance;
use App\Models\PublicHoliday;
use App\Models\Setting;
use App\Models\User;
use App\Services\NotificationService;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RavensDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Attendance summary using configured attendance period (default start day 25)
        $startDay = Setting::get('attendance_period_start_day', 25);
        $today = now();

        if ($today->day >= $startDay) {
            $periodStart = \Carbon\Carbon::create($today->year, $today->month, $startDay)->startOfDay();
            $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
        } else {
            $periodStart = \Carbon\Carbon::create($today->copy()->subMonth()->year, $today->copy()->subMonth()->month, $startDay)->startOfDay();
            $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
        }

        $attendanceRecords = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$periodStart->format('Y-m-d'), $periodEnd->format('Y-m-d')])
            ->get()
            ->keyBy(function($a) { return $a->date->format('Y-m-d'); });

        // Calculate actual workdays (excluding weekends and holidays)
        $workdays = 0;
        $present = 0;
        $late = 0;
        $absent = 0;
        $totalHours = 0;
        
        $cursor = $periodStart->copy();
        $now = \Carbon\Carbon::now('Asia/Karachi');
        
        // For night shift: if before 5am, we're still in previous day's shift
        $effectiveToday = $now->copy();
        if ($now->hour < 5) {
            $effectiveToday->subDay();
        }
        
        while ($cursor->lte($periodEnd)) {
            // Skip if future date (only check if viewing current period)
            // If cursor is beyond today, skip it (don't count future days)
            if ($cursor->gt($now)) {
                $cursor->addDay();
                continue;
            }
            
            // Skip weekends
            if (in_array($cursor->dayOfWeek, [\Carbon\Carbon::SATURDAY, \Carbon\Carbon::SUNDAY])) {
                $cursor->addDay();
                continue;
            }
            
            // Skip public holidays
            if (PublicHoliday::isHoliday($cursor)) {
                $cursor->addDay();
                continue;
            }
            
            $workdays++;
            $att = $attendanceRecords->get($cursor->format('Y-m-d'));
            
            if ($att) {
                if ($att->status === 'present') $present++;
                if ($att->status === 'late') $late++;
                $totalHours += $att->working_hours ?? 0;
            } else {
                $absent++;
            }
            
            $cursor->addDay();
        }

        $attendanceSummary = [
            'total_days' => $workdays,
            'total_records' => $attendanceRecords->count(), // Actual attendance records
            'present_days' => $present,
            'late_days' => $late,
            'absent_days' => $absent,
            'total_working_hours' => round($totalHours, 1),
            'average_working_hours' => $workdays > 0 ? round($totalHours / $workdays, 1) : 0,
        ];

        // Get stats for the Ravens employee
        $stats = [
            'dialed_today' => $this->getDialedTodayCount($user->id),
            'calls_connected' => $this->getCallsConnectedCount($user->id),
            'sales_today' => $this->getSalesTodayCount($user->id),
            'mtd_sales' => $this->getMTDSalesCount($user->id),
            'attendance_summary' => $attendanceSummary,
            // Today's attendance status if any
            'today_status' => Attendance::where('user_id', $user->id)->whereDate('date', today())->first(),
        ];

        // Get sales made by this closer for dashboard
        $mySales = Lead::where('closer_name', $user->name)
            ->whereNotNull('sale_at')
            ->orderBy('sale_at', 'desc')
            ->paginate(10);

        return view('ravens.dashboard', compact('stats', 'mySales'));
    }

    public function calling()
    {
        // Get all leads for Ravens employees to call
        // Exclude leads that have been marked as sold (status = 'accepted' and sale_at is not null)
        // UNLESS the current user is the one who closed it
        // Also exclude leads submitted by Peregrine closers (team = 'peregrine')
        $currentUser = Auth::user();
        
        $leads = Lead::where(function($query) use ($currentUser) {
            // Include leads that are not sold yet
            $query->where(function($q) {
                $q->whereNull('sale_at')
                  ->orWhere('status', '!=', 'accepted');
            })
            // OR include leads sold by current user (so they can see their own sales)
            ->orWhere('closer_name', $currentUser->name);
        })
        // Exclude Peregrine team leads
        ->where(function($query) {
            $query->where('team', '!=', 'peregrine')
                  ->orWhereNull('team');
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return view('ravens.calling', compact('leads'));
    }

    /**
     * Get count of unique leads dialed today by this employee
     */
    private function getDialedTodayCount($userId)
    {
        return CallLog::where('agent_id', $userId)
            ->whereDate('call_start_time', today())
            ->distinct('lead_id')
            ->count('lead_id');
    }

    /**
     * Get count of sales made today by this employee
     */
    private function getSalesTodayCount($userId)
    {
        return Lead::where('closer_name', Auth::user()->name)
            ->whereDate('sale_at', today())
            ->where('manager_status', 'approved')
            ->count();
    }

    /**
     * Get count of calls connected today
     */
    private function getCallsConnectedCount($userId)
    {
        return CallLog::where('agent_id', $userId)
            ->whereDate('call_start_time', today())
            ->where('call_status', 'connected')
            ->count();
    }

    /**
     * Get MTD (Month-To-Date) sales count for this employee
     */
    private function getMTDSalesCount($userId)
    {
        return Lead::where('closer_name', Auth::user()->name)
            ->whereMonth('sale_at', now()->month)
            ->whereYear('sale_at', now()->year)
            ->where('manager_status', 'approved')
            ->count();
    }

    /**
     * Get lead data for the Ravens form popup
     */
    public function getLeadData($leadId)
    {
        $lead = Lead::find($leadId);

        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }

        // Return full lead data for the form with properly formatted dates
        return response()->json([
            'id' => $lead->id,
            'cn_name' => $lead->cn_name,
            'phone_number' => $lead->phone_number,
            'date_of_birth' => $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('Y-m-d') : null,
            'ssn' => $lead->ssn,
            'gender' => $lead->gender,
            'beneficiaries' => $lead->beneficiaries ?? [], // Return beneficiaries array
            'carrier_name' => $lead->carrier_name,
            'coverage_amount' => $lead->coverage_amount,
            'monthly_premium' => $lead->monthly_premium,
            'birth_place' => $lead->birth_place,
            'smoker' => $lead->smoker,
            'height_weight' => $lead->height_weight,
            'address' => $lead->address,
            'medical_issue' => $lead->medical_issue,
            'medications' => $lead->medications,
            'doctor_name' => $lead->doctor_name,
            'doctor_address' => $lead->doctor_address,
            'policy_type' => $lead->policy_type,
            'initial_draft_date' => $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('Y-m-d') : null,
            'bank_name' => $lead->bank_name,
            'account_type' => $lead->account_type,
            'routing_number' => $lead->routing_number,
            'account_number' => $lead->acc_number,
            'verified_by' => $lead->account_verified_by,
            'bank_balance' => $lead->bank_balance,
            'source' => $lead->source,
            'closer_name' => $lead->closer_name,
        ]);
    }

    /**
     * Save lead data without marking as sale
     */
    public function saveLead(Request $request)
    {
        try {
            $leadId = $request->input('lead_id');
            $lead = Lead::findOrFail($leadId);

            // Prepare update data - only update fields that are provided and not empty
            $updateData = [];
            
            // Handle all possible fields from phase 2 and phase 3
            if ($request->filled('cn_name')) {
                $updateData['cn_name'] = $request->input('cn_name');
            }
            
            if ($request->filled('phone_number')) {
                $updateData['phone_number'] = $request->input('phone_number');
            }
            
            if ($request->filled('date_of_birth')) {
                $updateData['date_of_birth'] = $request->input('date_of_birth');
            }
            
            if ($request->filled('ssn')) {
                $updateData['ssn'] = $request->input('ssn');
            }
            
            if ($request->filled('gender')) {
                $updateData['gender'] = $request->input('gender');
            }
            
            if ($request->filled('address')) {
                $updateData['address'] = $request->input('address');
            }
            
            // Handle beneficiaries array
            if ($request->has('beneficiaries')) {
                $updateData['beneficiaries'] = json_encode($request->input('beneficiaries'));
            }
            
            if ($request->filled('policy_type')) {
                $updateData['policy_type'] = $request->input('policy_type');
            }
            
            if ($request->filled('carrier_name')) {
                $updateData['carrier_name'] = $request->input('carrier_name');
            }
            
            if ($request->filled('coverage_amount')) {
                $updateData['coverage_amount'] = $request->input('coverage_amount');
            }
            
            if ($request->filled('monthly_premium')) {
                $updateData['monthly_premium'] = $request->input('monthly_premium');
            }
            
            if ($request->filled('initial_draft_date')) {
                $updateData['initial_draft_date'] = $request->input('initial_draft_date');
            }
            
            if ($request->filled('bank_name')) {
                $updateData['bank_name'] = $request->input('bank_name');
            }
            
            if ($request->filled('account_type')) {
                $updateData['account_type'] = $request->input('account_type');
            }
            
            if ($request->filled('routing_number')) {
                $updateData['routing_number'] = $request->input('routing_number');
            }
            
            if ($request->filled('account_number')) {
                $updateData['account_number'] = $request->input('account_number');
            }
            
            if ($request->filled('account_verified_by')) {
                $updateData['account_verified_by'] = $request->input('account_verified_by');
            }
            
            if ($request->filled('bank_balance')) {
                $updateData['bank_balance'] = $request->input('bank_balance');
            }
            
            if ($request->filled('source')) {
                $updateData['source'] = $request->input('source');
            }
            
            if ($request->filled('closer_name')) {
                $updateData['closer_name'] = $request->input('closer_name');
            }

            if (!empty($updateData)) {
                $lead->update($updateData);
                
                \Log::info('Ravens lead saved', [
                    'lead_id' => $leadId,
                    'updated_fields' => array_keys($updateData),
                    'user_id' => Auth::id()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lead information saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving lead: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save lead information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit sale - Mark lead as sold and send to sales section
     */
    public function submitSale(Request $request)
    {
        try {
            $leadId = $request->input('lead_id');
            $lead = Lead::findOrFail($leadId);

            // Check for duplicate sales within 3 months using phone number and SSN
            $phone = $request->input('phone_number') ?? $lead->phone_number;
            $ssn = $request->input('ssn') ?? $lead->ssn;
            
            $repeatSale = $this->checkRepeatSale($phone, $ssn, $leadId);
            
            if ($repeatSale) {
                // Log repeat sale for admin review
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'repeat_sale_detected',
                    'model' => 'Lead',
                    'model_id' => $leadId,
                    'changes' => json_encode([
                        'message' => 'Repeat sale detected within 3 months',
                        'previous_sale_id' => $repeatSale->id,
                        'previous_sale_date' => $repeatSale->sale_at,
                        'previous_closer' => $repeatSale->closer_name,
                        'phone' => $phone,
                        'ssn' => $ssn,
                    ]),
                    'ip_address' => $request->ip(),
                ]);
            }

            // Update lead with all form data
            $updateData = [];
            
            if ($request->filled('cn_name')) {
                $updateData['cn_name'] = $request->input('cn_name');
            }
            
            if ($request->filled('phone_number')) {
                $updateData['phone_number'] = $request->input('phone_number');
            }
            
            if ($request->filled('date_of_birth')) {
                $updateData['date_of_birth'] = $request->input('date_of_birth');
            }
            
            if ($request->filled('ssn')) {
                $updateData['ssn'] = $request->input('ssn');
            }
            
            if ($request->filled('gender')) {
                $updateData['gender'] = $request->input('gender');
            }
            
            if ($request->filled('address')) {
                $updateData['address'] = $request->input('address');
            }
            
            // Handle beneficiaries array
            if ($request->has('beneficiaries')) {
                $updateData['beneficiaries'] = json_encode($request->input('beneficiaries'));
            }
            
            if ($request->filled('policy_type')) {
                $updateData['policy_type'] = $request->input('policy_type');
            }
            
            if ($request->filled('carrier_name')) {
                $updateData['carrier_name'] = $request->input('carrier_name');
            }
            
            if ($request->filled('coverage_amount')) {
                $updateData['coverage_amount'] = $request->input('coverage_amount');
            }
            
            if ($request->filled('monthly_premium')) {
                $updateData['monthly_premium'] = $request->input('monthly_premium');
            }
            
            if ($request->filled('initial_draft_date')) {
                $updateData['initial_draft_date'] = $request->input('initial_draft_date');
            }
            
            if ($request->filled('bank_name')) {
                $updateData['bank_name'] = $request->input('bank_name');
            }
            
            if ($request->filled('account_type')) {
                $updateData['account_type'] = $request->input('account_type');
            }
            
            if ($request->filled('routing_number')) {
                $updateData['routing_number'] = $request->input('routing_number');
            }
            
            if ($request->filled('account_number')) {
                $updateData['account_number'] = $request->input('account_number');
            }
            
            if ($request->filled('account_verified_by')) {
                $updateData['account_verified_by'] = $request->input('account_verified_by');
            }
            
            if ($request->filled('bank_balance')) {
                $updateData['bank_balance'] = $request->input('bank_balance');
            }
            
            if ($request->filled('source')) {
                $updateData['source'] = $request->input('source');
            }
            
            if ($request->filled('closer_name')) {
                $updateData['closer_name'] = $request->input('closer_name');
            }
            
            if ($request->filled('state')) {
                $updateData['state'] = $request->input('state');
            }

            // Mark as sold - pending status for QA review
            $updateData['status'] = 'pending'; // Sale status - pending QA review
            $updateData['sale_at'] = now();
            $updateData['sale_date'] = now()->format('Y-m-d');
            $updateData['team'] = 'ravens'; // Mark as Ravens team sale

            $lead->update($updateData);

            // Send notifications to QA and Managers
            $this->sendSaleNotifications($lead);

            // Log the sale submission
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'sale_submitted',
                'model' => 'Lead',
                'model_id' => $leadId,
                'changes' => json_encode([
                    'closer_name' => $request->input('closer_name'),
                    'sale_at' => now(),
                    'customer_name' => $request->input('cn_name'),
                ]),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sale submitted successfully! Notifications sent to QA and Managers.',
                'is_repeat_sale' => !is_null($repeatSale),
                'repeat_sale_message' => $repeatSale 
                    ? "Warning: This customer had a previous sale on " . $repeatSale->sale_at->format('M d, Y') . " by " . $repeatSale->closer_name 
                    : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error submitting sale: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check for repeat sales within 3 months
     */
    private function checkRepeatSale($phone, $ssn, $currentLeadId)
    {
        $threeMonthsAgo = now()->subMonths(3);

        // Search for previous sales by phone or SSN within 3 months
        $query = Lead::where('id', '!=', $currentLeadId)
            ->whereNotNull('sale_at')
            ->where('sale_at', '>=', $threeMonthsAgo);

        // Check by phone OR SSN
        $query->where(function($q) use ($phone, $ssn) {
            if ($phone) {
                $q->where('phone_number', $phone);
            }
            if ($ssn) {
                $q->orWhere('ssn', $ssn);
            }
        });

        return $query->first();
    }

    /**
     * Send notifications to QA and Managers about new sale
     */
    private function sendSaleNotifications($lead)
    {
        $notificationService = app(NotificationService::class);

        // Get QA users
        $qaUsers = User::role('QA')->get();
        
        // Get Managers
        $managers = User::role('Manager')->get();

        $message = "New sale submitted by " . Auth::user()->name . " for customer: " . $lead->cn_name;

        // Send to QA
        foreach ($qaUsers as $qaUser) {
            $notificationService->createForUser(
                $qaUser,
                'New Sale Submitted',
                $message,
                [
                    'icon' => 'bx-dollar-circle',
                    'color' => 'success',
                    'type' => 'success',
                    'url' => route('sales.index'),
                ]
            );
        }

        // Send to Managers
        foreach ($managers as $manager) {
            $notificationService->createForUser(
                $manager,
                'New Sale Submitted',
                $message,
                [
                    'icon' => 'bx-dollar-circle',
                    'color' => 'success',
                    'type' => 'success',
                    'url' => route('sales.index'),
                ]
            );
        }
    }

    /**
     * Dispose a lead with a reason (no answer, wrong number, wrong details)
     */
    public function disposeLead(Request $request)
    {
        try {
            $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'disposition' => 'required|in:no_answer,wrong_number,wrong_details',
                'notes' => 'nullable|string|max:500'
            ]);

            $lead = Lead::findOrFail($request->input('lead_id'));

            // Create a snapshot of lead data before disposing
            $leadSnapshot = $lead->only([
                'cn_name', 'phone_number', 'date_of_birth', 'ssn', 'gender',
                'address', 'state', 'policy_type', 'carrier_name', 'source'
            ]);

            // Create bad lead record
            $badLead = BadLead::create([
                'lead_id' => $lead->id,
                'disposed_by' => Auth::id(),
                'disposition' => $request->input('disposition'),
                'notes' => $request->input('notes'),
                'lead_name' => $lead->cn_name,
                'lead_phone' => $lead->phone_number,
                'lead_ssn' => $lead->ssn,
            ]);

            // Mark original lead as disposed (you can also delete it if preferred)
            $lead->update([
                'status' => 'disposed',
                'disposed_at' => now(),
                'disposed_by' => Auth::id(),
                'disposition_reason' => $request->input('disposition'),
            ]);

            // Log the disposition
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'lead_disposed',
                'model' => 'Lead',
                'model_id' => $lead->id,
                'changes' => json_encode([
                    'disposition' => $request->input('disposition'),
                    'notes' => $request->input('notes'),
                    'customer_name' => $lead->cn_name,
                ]),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead disposed successfully',
                'disposition' => BadLead::getDispositionLabel($request->input('disposition'))
            ]);
        } catch (\Exception $e) {
            Log::error('Error disposing lead: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to dispose lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View all bad/disposed leads
     */
    public function badLeads()
    {
        $badLeads = BadLead::with(['lead', 'disposedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('ravens.bad-leads', compact('badLeads'));
    }
}
