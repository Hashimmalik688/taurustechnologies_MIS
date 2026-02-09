<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    protected $fillable = ['setting_key', 'setting_value', 'description'];

    /**
     * Get or create total working days setting for current month
     */
    public static function getTotalWorkingDays($month, $year)
    {
        $key = "working_days_{$year}_{$month}";
        $setting = self::where('setting_key', $key)->first();
        
        if (!$setting) {
            $setting = self::create([
                'setting_key' => $key,
                'setting_value' => '22', // Default 22 working days
                'description' => "Total working days for {$month}/{$year}"
            ]);
        }
        
        return (int) $setting->setting_value;
    }

    /**
     * Update total working days for a month
     */
    public static function setTotalWorkingDays($month, $year, $days)
    {
        $key = "working_days_{$year}_{$month}";
        self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $days,
                'description' => "Total working days for {$month}/{$year}"
            ]
        );
    }
}
