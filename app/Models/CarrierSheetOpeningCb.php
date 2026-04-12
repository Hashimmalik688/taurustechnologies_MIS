<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrierSheetOpeningCb extends Model
{
    protected $table = 'carrier_sheet_opening_cbs';

    protected $fillable = [
        'carrier_sheet_rate_id',
        'period_month',
        'amount',
    ];

    protected $casts = [
        'period_month' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function carrierRate(): BelongsTo
    {
        return $this->belongsTo(CarrierSheetRate::class, 'carrier_sheet_rate_id');
    }
}
