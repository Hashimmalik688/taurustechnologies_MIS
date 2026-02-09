<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EPMSProject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'epms_projects';

    protected $fillable = [
        'name',
        'description',
        'client_name',
        'client_email',
        'client_phone',
        'region',
        'currency',
        'contract_value',
        'external_costs',
        'gross_profit',
        'margin_percentage',
        'start_date',
        'deadline',
        'estimated_completion_date',
        'status',
        'health_score',
        'project_velocity',
        'scope_creep_count',
        'total_tasks',
        'completed_tasks',
        'revision_tasks',
        'created_by',
        'project_manager_id',
    ];

    protected $casts = [
        'contract_value' => 'decimal:2',
        'external_costs' => 'decimal:2',
        'gross_profit' => 'decimal:2',
        'margin_percentage' => 'decimal:2',
        'project_velocity' => 'decimal:2',
        'start_date' => 'date',
        'deadline' => 'date',
        'estimated_completion_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function tasks()
    {
        return $this->hasMany(EPMSTask::class, 'project_id');
    }

    public function milestones()
    {
        return $this->hasMany(EPMSMilestone::class, 'project_id')->orderBy('order');
    }

    public function externalCosts()
    {
        return $this->hasMany(EPMSExternalCost::class, 'project_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    /**
     * Calculated Attributes
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->total_tasks == 0) {
            return 0;
        }
        return round(($this->completed_tasks / $this->total_tasks) * 100, 2);
    }

    public function getDaysRemainingAttribute()
    {
        return now()->diffInDays($this->deadline, false);
    }

    public function getTimeElapsedDaysAttribute()
    {
        return $this->start_date->diffInDays(now());
    }

    /**
     * Calculate and update project analytics
     */
    public function updateAnalytics()
    {
        // Count tasks
        $this->total_tasks = $this->tasks()->count();
        $this->completed_tasks = $this->tasks()->where('status', 'completed')->count();
        $this->revision_tasks = $this->tasks()->where('task_type', 'revision')->count();

        // Calculate velocity (tasks per day)
        $timeElapsed = max($this->time_elapsed_days, 1);
        $this->project_velocity = round($this->completed_tasks / $timeElapsed, 2);

        // Calculate estimated completion date
        if ($this->project_velocity > 0) {
            $remainingTasks = $this->total_tasks - $this->completed_tasks;
            $estimatedDays = ceil($remainingTasks / $this->project_velocity);
            $this->estimated_completion_date = now()->addDays($estimatedDays);
        }

        // Calculate health score
        $this->health_score = $this->calculateHealthScore();

        // Calculate scope creep
        $this->scope_creep_count = $this->revision_tasks;

        // Calculate financial metrics
        $this->external_costs = $this->externalCosts()->sum('amount');
        $this->gross_profit = $this->contract_value - $this->external_costs;
        
        if ($this->contract_value > 0) {
            $this->margin_percentage = round(($this->gross_profit / $this->contract_value) * 100, 2);
        }

        $this->save();
    }

    /**
     * Calculate health score based on progress and deadline
     */
    private function calculateHealthScore()
    {
        if ($this->total_tasks == 0) {
            return 'green';
        }

        $progressPercentage = $this->progress_percentage;
        $timeElapsedPercentage = ($this->time_elapsed_days / max($this->start_date->diffInDays($this->deadline), 1)) * 100;

        // Check if any milestone is missed
        if ($this->milestones()->where('status', 'missed')->exists()) {
            return 'red';
        }

        // Red: Deadline is close (<15% remaining) with >30% tasks remaining
        if ($progressPercentage < 70 && $timeElapsedPercentage > 85) {
            return 'red';
        }

        // Yellow: Progress is lagging behind time
        if ($progressPercentage < $timeElapsedPercentage - 15) {
            return 'yellow';
        }

        return 'green';
    }

    /**
     * Get client timezone based on region
     */
    public function getClientTimezoneAttribute()
    {
        return $this->region === 'US' ? 'America/New_York' : 'Asia/Karachi';
    }
}
