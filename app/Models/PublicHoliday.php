<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PublicHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Check if a given date is a public holiday
     * Checks both public_holidays and legacy holidays tables
     */
    public static function isHoliday($date): bool
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        
        // Check new public_holidays table
        $newHoliday = static::where('date', $date)
            ->where('is_active', true)
            ->exists();
            
        if ($newHoliday) {
            return true;
        }
        
        // Check legacy holidays table for backward compatibility
        $legacyHoliday = \App\Models\Holiday::where('date', $date)->exists();
        
        return $legacyHoliday;
    }

    /**
     * Get all active holidays for a given month
     */
    public static function getMonthHolidays($year, $month)
    {
        return static::where('is_active', true)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();
    }

    /**
     * Get upcoming holidays
     */
    public static function getUpcomingHolidays($limit = 5)
    {
        return static::where('is_active', true)
            ->where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->limit($limit)
            ->get();
    }
}
