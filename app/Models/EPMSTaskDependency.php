<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPMSTaskDependency extends Model
{
    use HasFactory;

    protected $table = 'epms_task_dependencies';

    protected $fillable = [
        'task_id',
        'depends_on_task_id',
        'dependency_type',
        'lag_days',
    ];

    /**
     * Relationships
     */
    public function task()
    {
        return $this->belongsTo(EPMSTask::class, 'task_id');
    }

    public function dependsOnTask()
    {
        return $this->belongsTo(EPMSTask::class, 'depends_on_task_id');
    }
}
