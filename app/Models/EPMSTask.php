<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPMSTask extends Model
{
    use HasFactory;

    protected $table = 'epms_tasks';

    protected $fillable = [
        'project_id',
        'milestone_id',
        'name',
        'description',
        'status',
        'priority',
        'task_type',
        'start_date',
        'end_date',
        'completed_at',
        'assigned_to',
        'progress',
        'estimated_hours',
        'actual_hours',
        'order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function project()
    {
        return $this->belongsTo(EPMSProject::class, 'project_id');
    }

    public function milestone()
    {
        return $this->belongsTo(EPMSMilestone::class, 'milestone_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function dependencies()
    {
        return $this->hasMany(EPMSTaskDependency::class, 'task_id');
    }

    public function dependents()
    {
        return $this->hasMany(EPMSTaskDependency::class, 'depends_on_task_id');
    }

    /**
     * Get all tasks that this task depends on
     */
    public function dependsOnTasks()
    {
        return $this->belongsToMany(
            EPMSTask::class,
            'epms_task_dependencies',
            'task_id',
            'depends_on_task_id'
        )->withPivot('dependency_type', 'lag_days');
    }

    /**
     * Get all tasks that depend on this task
     */
    public function dependentTasks()
    {
        return $this->belongsToMany(
            EPMSTask::class,
            'epms_task_dependencies',
            'depends_on_task_id',
            'task_id'
        )->withPivot('dependency_type', 'lag_days');
    }

    /**
     * Get duration in days
     */
    public function getDurationAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Check if task is overdue
     */
    public function getIsOverdueAttribute()
    {
        return $this->status !== 'completed' && $this->end_date->isPast();
    }

    /**
     * Auto-adjust dependent tasks when this task's dates change
     */
    public function adjustDependentTasks()
    {
        foreach ($this->dependentTasks as $dependentTask) {
            $dependency = $dependentTask->pivot;
            
            if ($dependency->dependency_type === 'finish-to-start') {
                // Dependent task should start after this task finishes
                $newStartDate = $this->end_date->copy()->addDays($dependency->lag_days + 1);
                $duration = $dependentTask->duration;
                
                $dependentTask->start_date = $newStartDate;
                $dependentTask->end_date = $newStartDate->copy()->addDays($duration - 1);
                $dependentTask->save();
                
                // Recursively adjust tasks that depend on this one
                $dependentTask->adjustDependentTasks();
            }
        }
    }
}
