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
        'trigger',
        'keeps_in_calling',
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
            // Legacy dispositions (removes from calling)
            'no_answer'      => 'No Answer',
            'wrong_number'   => 'Wrong Number',
            'wrong_details'  => 'Wrong Details',
            // End-call dispositions (keeps in calling)
            'answering_machine' => 'A - Answering Machine',
            'busy'              => 'B - Busy',
            'dead_air'          => 'DAIR - Dead Air',
            'disconnected'      => 'DC - Disconnected Number',
            'declined_sale'     => 'DEC - Declined Sale',
            'dnc'               => 'DNC - Do Not Call',
            'no_answer_ec'      => 'N - No Answer',
            'not_interested'    => 'NI - Not Interested',
            'no_pitch'          => 'NP - No Pitch No Price',
            'business_number'   => 'BN - Business Number',
            'not_in_service'    => 'NNIS - Number Not In Service',
            // Save & Exit dispositions
            'callback_set'      => 'Callback Set',
            'updated_data'      => 'Updated Data',
        ];

        return $labels[$disposition] ?? $disposition;
    }

    /** Dispositions valid for End Call trigger */
    public static function endCallDispositions(): array
    {
        return [
            'answering_machine', 'busy', 'dead_air', 'disconnected',
            'declined_sale', 'dnc', 'no_answer_ec', 'not_interested', 'no_pitch',
            'business_number', 'not_in_service',
        ];
    }

    /** Dispositions valid for Save & Exit trigger */
    public static function saveExitDispositions(): array
    {
        return ['callback_set', 'updated_data'];
    }
}
