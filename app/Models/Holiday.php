<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'name',
        'description',
        'is_recurring',
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
    ];

    /**
     * Check if a given date is a holiday
     */
    public static function isHoliday($date): bool
    {
        $date = Carbon::parse($date)->toDateString();
        return self::where('date', $date)->exists();
    }

    /**
     * Get upcoming holidays
     */
    public static function upcoming($limit = 5)
    {
        return self::where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->limit($limit)
            ->get();
    }
}
