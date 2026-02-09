<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PabsTicket;
use App\Models\PabsTicketComment;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of tickets
     */
    public function index(Request $request)
    {
        // Calculate KPI metrics - exclude closed tickets from priority counts
        $kpis = [
            'total_tickets' => PabsTicket::count(),
            'open_tickets' => PabsTicket::where('status', 'OPEN')->count(),
            'closed_tickets' => PabsTicket::where('status', 'CLOSED')->count(),
            'high_priority' => PabsTicket::whereNot('status', 'CLOSED')->where('priority', 'HIGH')->count(),
            'medium_priority' => PabsTicket::whereNot('status', 'CLOSED')->where('priority', 'MEDIUM')->count(),
            'low_priority' => PabsTicket::whereNot('status', 'CLOSED')->where('priority', 'LOW')->count(),
        ];
        
        $query = PabsTicket::with('creator', 'assignee');
        
        // Filter by section
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Search by ticket code or subject
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_code', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }
        
        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);
        $sections = \App\Services\ProjectAuthorizationService::getSections();
        
        return view('admin.pabs.tickets.index', compact('tickets', 'sections', 'kpis'));
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create()
    {
        $sections = \App\Services\ProjectAuthorizationService::getSections();
        $users = \App\Models\User::orderBy('name')->get();
        return view('admin.pabs.tickets.create', compact('sections', 'users'));
    }

    /**
     * Store a newly created ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'section_id' => 'required|integer|min:1|max:11',
            'priority' => 'required|in:HIGH,MEDIUM,LOW',
            'assigned_to' => 'required|exists:users,id',
            'total_cost' => 'nullable|numeric|min:0',
            'quote_amount' => 'nullable|numeric|min:0',
        ]);
        
        $ticketCode = $this->generateTicketCode();
        
        $ticket = PabsTicket::create([
            'ticket_code' => $ticketCode,
            'subject' => $request->subject,
            'description' => $request->description,
            'section_id' => $request->section_id,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'total_cost' => $request->total_cost,
            'quote_amount' => $request->quote_amount,
            'created_by' => auth()->id(),
            'status' => 'OPEN',
            'approval_status' => 'PENDING',
        ]);
        
        return redirect()->route('pabs.tickets.show', $ticket)->with('success', 'Ticket created successfully.');
    }

    /**
     * Display the specified ticket
     */
    public function show(PabsTicket $ticket)
    {
        $ticket->load('creator', 'assignee', 'comments.user');
        $sections = \App\Services\ProjectAuthorizationService::getSections();
        $users = \App\Models\User::orderBy('name')->get();
        
        return view('admin.pabs.tickets.show', compact('ticket', 'sections', 'users'));
    }

    /**
     * Update the specified ticket
     */
    public function update(Request $request, PabsTicket $ticket)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:HIGH,MEDIUM,LOW',
            'status' => 'required|in:OPEN,IN PROGRESS,ON HOLD,RESOLVED,CLOSED',
            'assigned_to' => 'nullable|exists:users,id',
            'total_cost' => 'nullable|numeric|min:0',
            'quote_amount' => 'nullable|numeric|min:0',
        ]);
        
        $updateData = $request->only('subject', 'description', 'priority', 'status', 'assigned_to', 'total_cost', 'quote_amount');
        
        // If resolving, add resolution timestamp
        if ($request->status === 'RESOLVED' && $ticket->status !== 'RESOLVED') {
            $updateData['resolved_at'] = now();
        }
        
        // If status changes from RESOLVED to something else, clear resolved_at
        if ($ticket->status === 'RESOLVED' && $request->status !== 'RESOLVED') {
            $updateData['resolved_at'] = null;
        }
        
        // Reset approval status if assigning to new user
        if ($request->filled('assigned_to') && $request->assigned_to !== $ticket->assigned_to) {
            $updateData['approval_status'] = 'PENDING';
            $updateData['approved_at'] = null;
        }
        
        $ticket->update($updateData);
        
        return redirect()->back()->with('success', 'Ticket updated successfully.');
    }

    /**
     * Add comment to ticket
     */
    public function addComment(Request $request, PabsTicket $ticket)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);
        
        PabsTicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);
        
        return redirect()->back()->with('success', 'Comment added.');
    }

    /**
     * Resolve ticket
     */
    public function resolve(Request $request, PabsTicket $ticket)
    {
        $request->validate([
            'resolution_notes' => 'required|string',
        ]);
        
        $ticket->update([
            'status' => 'RESOLVED',
            'resolution_notes' => $request->resolution_notes,
            'resolved_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Ticket resolved.');
    }

    /**
     * Close ticket
     */
    public function close(Request $request, PabsTicket $ticket)
    {
        $ticket->update(['status' => 'CLOSED']);
        
        return redirect()->back()->with('success', 'Ticket closed.');
    }

    /**
     * Approve ticket (by assigned user)
     */
    public function approve(Request $request, PabsTicket $ticket)
    {
        // Only assigned user can approve
        if ($ticket->assigned_to !== auth()->id()) {
            return redirect()->back()->with('error', 'Only assigned user can approve this ticket.');
        }

        $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        $ticket->update([
            'approval_status' => 'APPROVED',
            'approved_at' => now(),
            'approval_notes' => $request->approval_notes,
            'status' => 'CLOSED',
        ]);

        // Add system comment for approval
        PabsTicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => '[SYSTEM] Ticket accepted by ' . auth()->user()->name . '. Approval Notes: ' . ($request->approval_notes ?? 'None provided'),
        ]);

        return redirect()->back()->with('success', 'Ticket approved and automatically closed.');
    }

    /**
     * Reject ticket (by assigned user)
     */
    public function reject(Request $request, PabsTicket $ticket)
    {
        // Only assigned user can reject
        if ($ticket->assigned_to !== auth()->id()) {
            return redirect()->back()->with('error', 'Only assigned user can reject this ticket.');
        }

        $request->validate([
            'approval_notes' => 'required|string',
        ]);

        $ticket->update([
            'approval_status' => 'REJECTED',
            'approval_notes' => $request->approval_notes,
            'status' => 'CLOSED',
            'assigned_to' => null,
        ]);

        // Add system comment for rejection
        PabsTicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => '[SYSTEM] Ticket rejected by ' . auth()->user()->name . '. Rejection Reason: ' . $request->approval_notes,
        ]);

        return redirect()->back()->with('success', 'Ticket rejected and automatically closed.');
    }

    /**
     * Delete ticket
     */
    public function destroy(PabsTicket $ticket)
    {
        // Only creator or admin can delete
        if ($ticket->created_by !== auth()->id() && !auth()->user()->hasRole(['Super Admin', 'CEO'])) {
            return redirect()->back()->with('error', 'Unauthorized to delete this ticket.');
        }

        $ticketCode = $ticket->ticket_code;
        $ticket->delete();

        return redirect()->route('pabs.tickets.index')->with('success', "Ticket {$ticketCode} deleted successfully.");
    }

    /**
     * Generate unique ticket code: TICKET-[YEAR]-[SERIAL]
     */
    private function generateTicketCode()
    {
        $year = date('Y');
        
        $count = PabsTicket::whereYear('created_at', $year)->count();
        $serial = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        
        return "TICKET-{$year}-{$serial}";
    }
}
