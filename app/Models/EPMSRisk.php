<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPMSRisk extends Model
{
    use HasFactory;

    protected $table = 'epms_risks';

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'probability',
        'impact',
        'severity_score',
        'mitigation_plan',
        'contingency_plan',
        'owner_id',
        'status',
        'category',
        'identified_date',
        'resolved_date',
    ];

    protected $casts = [
        'identified_date' => 'date',
        'resolved_date' => 'date',
    ];

    // Probability/Impact numeric mapping
    const SCORE_MAP = [
        'very_low' => 1,
        'low' => 2,
        'medium' => 3,
        'high' => 4,
        'very_high' => 5,
    ];

    public function project()
    {
        return $this->belongsTo(EPMSProject::class, 'project_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Calculate severity score from probability and impact
     */
    public function calculateSeverity(): int
    {
        $prob = self::SCORE_MAP[$this->probability] ?? 3;
        $imp = self::SCORE_MAP[$this->impact] ?? 3;
        return $prob * $imp;
    }

    /**
     * Get severity level label
     */
    public function getSeverityLevelAttribute(): string
    {
        $score = $this->severity_score;
        if ($score >= 20) return 'critical';
        if ($score >= 12) return 'high';
        if ($score >= 6) return 'medium';
        return 'low';
    }

    /**
     * Auto-calculate severity on save
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($risk) {
            $risk->severity_score = $risk->calculateSeverity();
            if (!$risk->identified_date) {
                $risk->identified_date = now();
            }
        });
    }
}
