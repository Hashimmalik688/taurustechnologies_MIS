<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'disposed_by',
        'disposition',
        'notes',
        'lead_name',
        'lead_phone',
        'lead_ssn',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function disposedBy()
    {
        return $this->belongsTo(User::class, 'disposed_by');
    }

    public static function getDispositionLabel($disposition)
    {
        $labels = [
            'no_answer' => 'No Answer',
            'wrong_number' => 'Wrong Number',
            'wrong_details' => 'Wrong Details',
        ];
        
        return $labels[$disposition] ?? $disposition;
    }
}
