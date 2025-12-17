<?php

namespace App\Models;

use App\Traits\SanitizesPhoneNumbers;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasRoles, Notifiable, SanitizesPhoneNumbers, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dob',
        'avatar',
        'basic_salary',
        'target_sales',
        'bonus_per_extra_sale',
        'punctuality_bonus',
        'fine_per_absence',
        'fine_per_late',
        'salary_start_date',
        'salary_end_date',
        'payday_date',
        'is_sales_employee',
        'status',
        'last_login_at',
        'last_login_ip',
        'current_session_ip',
        'time_in',
        'time_out',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'salary_start_date' => 'date',
        'salary_end_date' => 'date',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function todayAttendance()
    {
        return $this->hasOne(Attendance::class)
            ->where('date', Carbon::today());
    }

    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    /**
     * Get sanitized zoom number for channel names
     */
    public function getSanitizedZoomNumberAttribute()
    {
        return $this->sanitizePhoneForChannel($this->zoom_number);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    /**
     * Get leads forwarded by this user
     */
    public function leadsForwarded()
    {
        return $this->hasMany(Lead::class, 'forwarded_by');
    }

    /**
     * Get leads managed by this user
     */
    public function leadsManaged()
    {
        return $this->hasMany(Lead::class, 'managed_by');
    }

    /**
     * Get salary records for this user
     */
    public function salaryRecords()
    {
        return $this->hasMany(SalaryRecord::class);
    }

    /**
     * Get carriers forwarded by this user
     */
    public function carriersForwarded()
    {
        return $this->hasMany(Carrier::class, 'forwarded_by');
    }

    /**
     * Get carriers managed by this user
     */
    public function carriersManaged()
    {
        return $this->hasMany(Carrier::class, 'managed_by');
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Get recent notifications (last 5).
     */
    public function getRecentNotificationsAttribute()
    {
        return $this->notifications()->take(5)->get();
    }

    /**
     * Create a custom notification for this user.
     * Renamed from notify() to avoid conflict with Laravel's Notifiable trait.
     */
    public function createNotification($title, $message, $options = [])
    {
        return Notification::createForUser($this->id, $title, $message, $options);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsAsRead()
    {
        return $this->notifications()->unread()->update(['read_at' => now()]);
    }

    /**
     * Get agent-specific commission rates for insurance carriers
     */
    public function carrierCommissions()
    {
        return $this->hasMany(AgentCarrierCommission::class);
    }

    /**
     * Get commission percentage for specific carrier
     */
    public function getCommissionForCarrier($carrierId)
    {
        $commission = $this->carrierCommissions()
            ->where('insurance_carrier_id', $carrierId)
            ->first();

        if ($commission) {
            return $commission->commission_percentage;
        }

        // Fallback to carrier's base commission
        $carrier = InsuranceCarrier::find($carrierId);
        return $carrier ? $carrier->base_commission_percentage : 0;
    }
}