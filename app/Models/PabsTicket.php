<?php

namespace App\Models;

use App\Support\Statuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PabsTicket extends Model
{
    use HasFactory;

    protected $table = 'pabs_tickets';

    protected $fillable = [
        'ticket_code',
        'subject',
        'description',
        'section_id',
        'project_id',
        'total_cost',
        'quote_amount',
        'status',
        'priority',
        'approval_status',
        'created_by',
        'assigned_to',
        'resolution_notes',
        'resolved_at',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'approved_at' => 'datetime',
        'total_cost' => 'decimal:2',
        'quote_amount' => 'decimal:2',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(PabsProject::class, 'project_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(PabsTicketComment::class, 'ticket_id');
    }

    // Scopes
    public function scopeBySection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', Statuses::TICKET_OPEN_STATUSES);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', Statuses::TICKET_CLOSED_STATUSES);
    }
}
