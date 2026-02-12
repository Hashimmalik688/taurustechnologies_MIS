<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPMSDocument extends Model
{
    use HasFactory;

    protected $table = 'epms_documents';

    protected $fillable = [
        'project_id',
        'task_id',
        'name',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'version',
        'uploaded_by',
        'category',
    ];

    public function project()
    {
        return $this->belongsTo(EPMSProject::class, 'project_id');
    }

    public function task()
    {
        return $this->belongsTo(EPMSTask::class, 'task_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
