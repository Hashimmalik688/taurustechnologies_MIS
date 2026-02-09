<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PabsProjectApproval extends Model
{
    use HasFactory;

    protected $table = 'pabs_project_approvals';

    protected $fillable = [
        'project_id',
        'approved_by',
        'action',
        'comments',
        'approved_budget',
        'target_deadline',
        'priority',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'target_deadline' => 'date',
        'approved_budget' => 'decimal:2',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(PabsProject::class, 'project_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
