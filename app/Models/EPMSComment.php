<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPMSComment extends Model
{
    use HasFactory;

    protected $table = 'epms_comments';

    protected $fillable = [
        'project_id',
        'task_id',
        'user_id',
        'body',
        'type',
    ];

    public function project()
    {
        return $this->belongsTo(EPMSProject::class, 'project_id');
    }

    public function task()
    {
        return $this->belongsTo(EPMSTask::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
