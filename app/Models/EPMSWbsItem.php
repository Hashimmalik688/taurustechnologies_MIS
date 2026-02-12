<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPMSWbsItem extends Model
{
    use HasFactory;

    protected $table = 'epms_wbs_items';

    protected $fillable = [
        'project_id',
        'parent_id',
        'code',
        'name',
        'description',
        'level',
        'estimated_cost',
        'actual_cost',
        'estimated_hours',
        'actual_hours',
        'progress',
        'order',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(EPMSProject::class, 'project_id');
    }

    public function parent()
    {
        return $this->belongsTo(EPMSWbsItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(EPMSWbsItem::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get all descendants recursively
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get depth level in tree
     */
    public function getDepthAttribute(): int
    {
        return substr_count($this->code, '.');
    }

    /**
     * Get cost variance
     */
    public function getCostVarianceAttribute(): float
    {
        return $this->estimated_cost - $this->actual_cost;
    }
}
