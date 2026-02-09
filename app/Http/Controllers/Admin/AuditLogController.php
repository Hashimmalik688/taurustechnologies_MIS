<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display all audit logs (Super Admin only)
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Paginate
        $auditLogs = $query->paginate(50);

        // Get available actions for filter
        $actions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.audit-logs.index', compact('auditLogs', 'actions'));
    }

    /**
     * Display detailed view of single audit log
     */
    public function show($id)
    {
        $auditLog = AuditLog::with('user')->findOrFail($id);
        return view('admin.audit-logs.show', compact('auditLog'));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        $query = AuditLog::with('user');

        // Apply filters
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->get();

        $csv = "ID,User,Email,Action,Model,Model ID,IP Address,Description,Created At\n";
        foreach ($logs as $log) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $log->id,
                $log->user?->name ?? 'System',
                $log->user_email,
                $log->action,
                $log->model ?? '',
                $log->model_id ?? '',
                $log->ip_address ?? '',
                str_replace('"', '""', $log->description ?? ''),
                $log->created_at->format('Y-m-d H:i:s')
            );
        }

        return response()->streamDownload(
            fn() => print $csv,
            'audit-logs-' . now()->format('Y-m-d-H-i-s') . '.csv',
            ['Content-Type' => 'text/csv']
        );
    }
    /**
     * Show device fingerprints used by multiple users (Account Switching Log)
     * Super Admin only
     */
    public function accountSwitchingLog()
    {
        // Group by device_fingerprint, get all with >1 user
        $suspicious = \App\Models\AuditLog::select('device_fingerprint')
            ->whereNotNull('device_fingerprint')
            ->groupBy('device_fingerprint')
            ->havingRaw('COUNT(DISTINCT user_id) > 1')
            ->pluck('device_fingerprint');

        // Get all login events for these fingerprints
        $logs = \App\Models\AuditLog::with('user')
            ->whereIn('device_fingerprint', $suspicious)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.account-switching-log', compact('logs'));
    }
}
