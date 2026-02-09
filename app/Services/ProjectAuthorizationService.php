<?php

namespace App\Services;

use App\Models\PabsProject;
use App\Models\PabsProjectApproval;
use App\Models\PabsProjectComment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectAuthorizationService
{
    /**
     * The 11 organizational domains (sections)
     */
    public static function getSections()
    {
        return [
            1 => 'Office Upgradations',
            2 => 'Employees',
            3 => 'Accounts',
            4 => 'Govt Liaison and Fee',
            5 => 'Vendors',
            6 => 'Clients',
            7 => 'IT and Equipment',
            8 => 'R and D',
            9 => 'Legal and Compliance',
            10 => 'Marketing and Events',
            11 => 'Utilities and General Admin',
        ];
    }

    /**
     * Generate unique project code: [SEC-ID]-[YEAR]-[SERIAL]
     * Example: SEC-01-2026-0001
     */
    public function generateProjectCode($sectionId)
    {
        $year = date('Y');
        
        // Get the count of projects for this section in this year
        $count = PabsProject::where('section_id', $sectionId)
            ->whereYear('created_at', $year)
            ->count();
        
        $serial = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        $sectionCode = str_pad($sectionId, 2, '0', STR_PAD_LEFT);
        
        return "SEC-{$sectionCode}-{$year}-{$serial}";
    }

    /**
     * Create a new project (Step 1: Initiation)
     */
    public function createProject($data, $userId)
    {
        return DB::transaction(function () use ($data, $userId) {
            $projectCode = $this->generateProjectCode($data['section_id']);
            
            $project = PabsProject::create([
                'project_code' => $projectCode,
                'project_name' => $data['project_name'],
                'description' => $data['description'],
                'section_id' => $data['section_id'],
                'status' => 'DRAFT',
                'created_by' => $userId,
                'total_budget' => $data['total_budget'] ?? null,
            ]);
            
            return $project;
        });
    }

    /**
     * Move project to Scoping (Step 2)
     */
    public function moveToScoping($project)
    {
        return $project->update(['status' => 'SCOPING']);
    }

    /**
     * Complete scoping and move to Quoting (Step 3)
     */
    public function completeScopingAndQuote($project, $scopingDocumentPath, $userId)
    {
        return DB::transaction(function () use ($project, $scopingDocumentPath, $userId) {
            $project->update([
                'scoping_document_path' => $scopingDocumentPath,
                'scoping_completed_at' => now(),
                'scoping_lead_id' => $userId,
                'status' => 'QUOTING',
            ]);
            
            return $project;
        });
    }

    /**
     * Add vendor quotes (Step 3: Quoting)
     */
    public function addVendorQuotes($project, $quotes)
    {
        $data = [];
        
        if (isset($quotes['vendor_a_name']) && isset($quotes['vendor_a_quote'])) {
            $data['vendor_a_name'] = $quotes['vendor_a_name'];
            $data['vendor_a_quote'] = $quotes['vendor_a_quote'];
        }
        
        if (isset($quotes['vendor_b_name']) && isset($quotes['vendor_b_quote'])) {
            $data['vendor_b_name'] = $quotes['vendor_b_name'];
            $data['vendor_b_quote'] = $quotes['vendor_b_quote'];
        }
        
        if (isset($quotes['vendor_c_name']) && isset($quotes['vendor_c_quote'])) {
            $data['vendor_c_name'] = $quotes['vendor_c_name'];
            $data['vendor_c_quote'] = $quotes['vendor_c_quote'];
        }
        
        return $project->update($data);
    }

    /**
     * Move to Pending Approval (Step 4: Executive Review)
     */
    public function moveToPendingApproval($project)
    {
        return $project->update(['status' => 'PENDING APPROVAL']);
    }

    /**
     * CEO Approval (Step 4: Executive Review)
     */
    public function approveProject($project, $approverId, $approvalData)
    {
        return DB::transaction(function () use ($project, $approverId, $approvalData) {
            // Create approval record for audit trail
            PabsProjectApproval::create([
                'project_id' => $project->id,
                'approved_by' => $approverId,
                'action' => $approvalData['approval_status'] ?? 'APPROVED',
                'comments' => $approvalData['approval_notes'] ?? null,
                'approved_budget' => $approvalData['approved_budget'] ?? null,
                'target_deadline' => $approvalData['target_deadline'] ?? null,
                'priority' => $approvalData['priority'] ?? 'MEDIUM',
                'approved_at' => now(),
            ]);
            
            // Update project status and details
            $updateData = [
                'approved_by' => $approverId,
                'approved_at' => now(),
                'approval_status' => $approvalData['approval_status'] ?? 'APPROVED',
                'approval_notes' => $approvalData['approval_notes'] ?? null,
                'approved_budget' => $approvalData['approved_budget'] ?? null,
                'target_deadline' => $approvalData['target_deadline'] ?? null,
                'priority' => $approvalData['priority'] ?? 'MEDIUM',
            ];
            
            // If approved, move to next status
            if (($approvalData['approval_status'] ?? 'APPROVED') === 'APPROVED') {
                $updateData['status'] = 'BUDGET ALLOCATED';
                $updateData['allocated_by'] = $approverId;
                $updateData['allocated_at'] = now();
            } else {
                // If not approved, keep in pending or set status appropriately
                $updateData['status'] = 'PENDING APPROVAL';
            }
            
            $project->update($updateData);
            
            return $project;
        });
    }

    /**
     * Reject project approval
     */
    public function rejectProject($project, $rejectingUserId, $reason)
    {
        return DB::transaction(function () use ($project, $rejectingUserId, $reason) {
            PabsProjectApproval::create([
                'project_id' => $project->id,
                'approved_by' => $rejectingUserId,
                'action' => 'REJECTED',
                'comments' => $reason,
                'approved_at' => now(),
            ]);
            
            $project->update([
                'status' => 'DRAFT',
                'approval_status' => 'REJECTED',
                'approval_notes' => $reason,
            ]);
            
            return $project;
        });
    }

    /**
     * Request clarification on approval
     */
    public function requestClarification($project, $userId, $clarification)
    {
        return DB::transaction(function () use ($project, $userId, $clarification) {
            PabsProjectApproval::create([
                'project_id' => $project->id,
                'approved_by' => $userId,
                'action' => 'CLARIFICATION NEEDED',
                'comments' => $clarification,
                'approved_at' => now(),
            ]);
            
            $project->update([
                'status' => 'PENDING APPROVAL',
                'approval_status' => 'CLARIFICATION NEEDED',
                'approval_notes' => $clarification,
            ]);
            
            return $project;
        });
    }

    /**
     * Move to execution (Step 6: Execution)
     */
    public function startExecution($project, $assignedToUserId)
    {
        return $project->update([
            'status' => 'IN PROGRESS',
            'assigned_to' => $assignedToUserId,
            'started_at' => now(),
        ]);
    }

    /**
     * Update execution progress with comments
     */
    public function addProgressComment($project, $userId, $comment)
    {
        return PabsProjectComment::create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'comment' => $comment,
        ]);
    }

    /**
     * Complete project and check for variance (Step 7: Verification & Closure)
     */
    public function completeProject($project, $actualCost, $finalNotes = null)
    {
        return DB::transaction(function () use ($project, $actualCost, $finalNotes) {
            $project->update([
                'actual_cost' => $actualCost,
                'completed_at' => now(),
            ]);
            
            // Check for variance
            if ($project->approved_budget && $actualCost > $project->approved_budget) {
                $varianceAmount = $actualCost - $project->approved_budget;
                $variancePercentage = ($varianceAmount / $project->approved_budget) * 100;
                
                $project->update([
                    'variance_flagged' => true,
                    'variance_notes' => "Variance of {$variancePercentage}% detected. Actual: ${$actualCost}, Approved: ${$project->approved_budget}. {$finalNotes}",
                    'status' => 'COMPLETED', // Flag will still be checked before archiving
                ]);
                
                return [
                    'success' => true,
                    'flagged' => true,
                    'variance_amount' => $varianceAmount,
                    'variance_percentage' => $variancePercentage,
                ];
            }
            
            $project->update(['status' => 'COMPLETED']);
            
            return [
                'success' => true,
                'flagged' => false,
            ];
        });
    }

    /**
     * Archive completed project
     */
    public function archiveProject($project)
    {
        return $project->update(['status' => 'ARCHIVED']);
    }

    /**
     * Get section name by ID
     */
    public function getSectionName($sectionId)
    {
        $sections = self::getSections();
        return $sections[$sectionId] ?? 'Unknown Section';
    }
}
