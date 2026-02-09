<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PettyCashLedger extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'serial_number',
        'date',
        'description',
        'head',
        'debit',
        'credit',
        'balance',
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'balance' => 'decimal:2',
    ];
}
