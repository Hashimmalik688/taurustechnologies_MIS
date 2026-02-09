<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPMSExternalCost extends Model
{
    use HasFactory;

    protected $table = 'epms_external_costs';

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'cost_type',
        'amount',
        'currency',
        'incurred_date',
        'vendor_name',
        'is_recurring',
        'recurring_period',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'incurred_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function project()
    {
        return $this->belongsTo(EPMSProject::class, 'project_id');
    }
}
