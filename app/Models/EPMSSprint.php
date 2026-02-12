<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPMSSprint extends Model
{
    use HasFactory;

    protected $table = 'epms_sprints';

    protected $fillable = [
        'project_id',
        'name',
        'goal',
        'start_date',
        'end_date',
        'status',
        'capacity_points',
        'completed_points',
        'sprint_number',
        'retrospective_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(EPMSProject::class, 'project_id');
    }

    public function tasks()
    {
        return $this->hasMany(EPMSTask::class, 'sprint_id');
    }

    public function getDurationDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->capacity_points == 0) return 0;
        return round(($this->completed_points / $this->capacity_points) * 100, 1);
    }

    /**
     * Get burndown data for the sprint
     */
    public function getBurndownData(): array
    {
        $totalPoints = $this->tasks()->sum('story_points');
        $days = [];
        $current = $this->start_date->copy();
        $end = min($this->end_date, now());

        while ($current->lte($end)) {
            $completedByDate = $this->tasks()
                ->where('status', 'completed')
                ->whereDate('completed_at', '<=', $current)
                ->sum('story_points');

            $days[] = [
                'date' => $current->format('M d'),
                'remaining' => $totalPoints - $completedByDate,
                'ideal' => $totalPoints - ($totalPoints * ($this->start_date->diffInDays($current) / max($this->duration_days, 1))),
            ];
            $current->addDay();
        }

        return $days;
    }

    /**
     * Update completed points from tasks
     */
    public function updateCompletedPoints(): void
    {
        $this->completed_points = $this->tasks()
            ->where('status', 'completed')
            ->sum('story_points');
        $this->save();
    }
}
