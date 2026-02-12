<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPMSProjectMember extends Model
{
    use HasFactory;

    protected $table = 'epms_project_members';

    protected $fillable = [
        'project_id',
        'user_id',
        'raci_role',
        'project_role',
        'is_lead',
    ];

    protected $casts = [
        'is_lead' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(EPMSProject::class, 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
