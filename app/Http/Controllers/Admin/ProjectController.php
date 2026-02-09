<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PabsProject;
use App\Services\ProjectAuthorizationService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectAuthorizationService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of all projects
     */
    public function index(Request $request)
    {
        $query = PabsProject::with('creator', 'approver', 'assignedTo');
        
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
        
        // Search by project code or name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('project_code', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%");
            });
        }
        
        $projects = $query->orderBy('created_at', 'desc')->paginate(20);
        $sections = ProjectAuthorizationService::getSections();
        
        return view('admin.pabs.projects.index', compact('projects', 'sections'));
    }

    /**
     * Show the form for creating a new project
     */
    public function create()
    {
        $sections = ProjectAuthorizationService::getSections();
        return view('admin.pabs.projects.create', compact('sections'));
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'required|string',
            'section_id' => 'required|integer|min:1|max:11',
            'total_budget' => 'nullable|numeric|min:0',
        ]);
        
        $project = $this->projectService->createProject($request->all(), auth()->id());
        
        return redirect()->route('pabs.projects.show', $project)->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified project
     */
    public function show(PabsProject $project)
    {
        $project->load('creator', 'scopingLead', 'approver', 'allocatedBy', 'assignedTo', 'approvals', 'comments.user', 'tickets');
        $sections = ProjectAuthorizationService::getSections();
        
        return view('admin.pabs.projects.show', compact('project', 'sections'));
    }

    /**
     * Show the form for editing the project
     */
    public function edit(PabsProject $project)
    {
        $sections = ProjectAuthorizationService::getSections();
        return view('admin.pabs.projects.edit', compact('project', 'sections'));
    }

    /**
     * Update the specified project
     */
    public function update(Request $request, PabsProject $project)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'required|string',
            'total_budget' => 'nullable|numeric|min:0',
        ]);
        
        $project->update($request->only('project_name', 'description', 'total_budget'));
        
        return redirect()->route('pabs.projects.show', $project)->with('success', 'Project updated successfully.');
    }

    /**
     * Move project to Scoping status
     */
    public function moveToScoping(Request $request, PabsProject $project)
    {
        $this->projectService->moveToScoping($project);
        
        return redirect()->back()->with('success', 'Project moved to Scoping.');
    }

    /**
     * Complete scoping
     */
    public function completeScopingAndQuote(Request $request, PabsProject $project)
    {
        $request->validate([
            'scoping_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        
        $path = $request->file('scoping_document')->store('pabs/scoping', 'public');
        
        $this->projectService->completeScopingAndQuote($project, $path, auth()->id());
        
        return redirect()->back()->with('success', 'Scoping completed and project moved to Quoting.');
    }

    /**
     * Add vendor quotes
     */
    public function addQuotes(Request $request, PabsProject $project)
    {
        $request->validate([
            'vendor_a_name' => 'nullable|string|max:255',
            'vendor_a_quote' => 'nullable|numeric|min:0',
            'vendor_b_name' => 'nullable|string|max:255',
            'vendor_b_quote' => 'nullable|numeric|min:0',
            'vendor_c_name' => 'nullable|string|max:255',
            'vendor_c_quote' => 'nullable|numeric|min:0',
        ]);
        
        $this->projectService->addVendorQuotes($project, $request->all());
        
        if ($request->filled('move_to_approval')) {
            $this->projectService->moveToPendingApproval($project);
        }
        
        return redirect()->back()->with('success', 'Vendor quotes added successfully.');
    }

    /**
     * CEO approval page
     */
    public function approval(PabsProject $project)
    {
        $project->load('creator', 'approvals');
        $sections = ProjectAuthorizationService::getSections();
        
        return view('admin.pabs.projects.approval', compact('project', 'sections'));
    }

    /**
     * Process approval
     */
    public function processApproval(Request $request, PabsProject $project)
    {
        $request->validate([
            'approval_status' => 'required|in:APPROVED,REJECTED,CLARIFICATION NEEDED',
            'approved_budget' => 'required_if:approval_status,APPROVED|numeric|min:0',
            'target_deadline' => 'required_if:approval_status,APPROVED|date',
            'priority' => 'required_if:approval_status,APPROVED|in:HIGH,MEDIUM,LOW',
            'approval_notes' => 'nullable|string',
        ]);
        
        if ($request->approval_status === 'APPROVED') {
            $this->projectService->approveProject($project, auth()->id(), $request->all());
            $message = 'Project approved successfully.';
        } elseif ($request->approval_status === 'REJECTED') {
            $this->projectService->rejectProject($project, auth()->id(), $request->approval_notes);
            $message = 'Project rejected.';
        } else {
            $this->projectService->requestClarification($project, auth()->id(), $request->approval_notes);
            $message = 'Clarification requested.';
        }
        
        return redirect()->route('pabs.projects.show', $project)->with('success', $message);
    }

    /**
     * Start execution
     */
    public function startExecution(Request $request, PabsProject $project)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);
        
        $this->projectService->startExecution($project, $request->assigned_to);
        
        return redirect()->back()->with('success', 'Project execution started.');
    }

    /**
     * Add progress comment
     */
    public function addComment(Request $request, PabsProject $project)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);
        
        $this->projectService->addProgressComment($project, auth()->id(), $request->comment);
        
        return redirect()->back()->with('success', 'Comment added.');
    }

    /**
     * Complete project
     */
    public function complete(Request $request, PabsProject $project)
    {
        $request->validate([
            'actual_cost' => 'required|numeric|min:0',
            'final_notes' => 'nullable|string',
        ]);
        
        $result = $this->projectService->completeProject($project, $request->actual_cost, $request->final_notes);
        
        if ($result['flagged']) {
            $message = "Project completed with variance warning. Variance: {$result['variance_percentage']}%";
        } else {
            $message = 'Project completed successfully.';
        }
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Archive completed project
     */
    public function archive(PabsProject $project)
    {
        $this->projectService->archiveProject($project);
        
        return redirect()->route('pabs.projects.index')->with('success', 'Project archived.');
    }
}
