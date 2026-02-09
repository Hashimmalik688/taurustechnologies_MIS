<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPMSMilestone extends Model
{
    use HasFactory;

    protected $table = 'epms_milestones';

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'due_date',
        'completed_at',
        'status',
        'order',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'date',
    ];

    /**
     * Relationships
     */
    public function project()
    {
        return $this->belongsTo(EPMSProject::class, 'project_id');
    }

    public function tasks()
    {
        return $this->hasMany(EPMSTask::class, 'milestone_id');
    }

    /**
     * Check if milestone is overdue
     */
    public function getIsOverdueAttribute()
    {
        return $this->status !== 'completed' && $this->due_date->isPast();
    }

    /**
     * Auto-update milestone status based on tasks
     */
    public function updateStatus()
    {
        $totalTasks = $this->tasks()->count();
        
        if ($totalTasks === 0) {
            return;
        }

        $completedTasks = $this->tasks()->where('status', 'completed')->count();

        if ($completedTasks === $totalTasks) {
            $this->status = 'completed';
            $this->completed_at = now();
        } elseif ($this->due_date->isPast()) {
            $this->status = 'missed';
        } else {
            $this->status = 'pending';
        }

        $this->save();
    }

    /**
     * Adjust milestone date and cascade to dependent tasks
     */
    public function adjustDate($newDate)
    {
        $daysDiff = $this->due_date->diffInDays($newDate, false);
        $this->due_date = $newDate;
        $this->save();

        // Adjust all tasks in this milestone
        foreach ($this->tasks as $task) {
            $task->start_date = $task->start_date->addDays($daysDiff);
            $task->end_date = $task->end_date->addDays($daysDiff);
            $task->save();
            
            // Cascade to dependent tasks
            $task->adjustDependentTasks();
        }
    }
}
