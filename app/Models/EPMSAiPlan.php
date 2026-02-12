<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPMSAiPlan extends Model
{
    use HasFactory;

    protected $table = 'epms_ai_plans';

    protected $fillable = [
        'project_id',
        'generated_by',
        'prompt',
        'response',
        'plan_data',
        'status',
    ];

    protected $casts = [
        'plan_data' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(EPMSProject::class, 'project_id');
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
