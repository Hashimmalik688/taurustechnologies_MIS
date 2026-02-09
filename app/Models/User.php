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
        'is_partner', // Flag for legacy partner records (should not be used for new partners)
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
        'bonus_payday_date',
        'is_sales_employee',
        'status',
        'last_login_at',
        'last_login_ip',
        'current_session_ip',
        'time_in',
        'time_out',
        'salary_advance',
        'tax_deduction',
        'is_qualified_for_punctuality',
        'working_days_monthly',
        'override_punctuality_bonus',
        'other_deductions',
        'other_allowances',
        'payroll_notes',
        'full_days',
        'half_days',
        'late_days',
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
        'is_partner' => 'boolean',
    ];

    /**
     * Set the user's email address.
     * Always convert to lowercase for consistency.
     *
     * @param  string  $value
     * @return void
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    /**
     * Scope to exclude partner records from user queries
     * Partners should exist ONLY in the partners table
     */
    public function scopeExcludePartners($query)
    {
        return $query->where('is_partner', false);
    }

    /**
     * Scope to get only actual employees (not partners)
     */
    public function scopeEmployeesOnly($query)
    {
        return $query->where('is_partner', false);
    }

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
     * Get chat participants for this user
     */
    public function chatParticipants()
    {
        return $this->hasMany(ChatParticipant::class);
    }

    /**
     * Get chat conversations for this user
     */
    public function chatConversations()
    {
        return $this->belongsToMany(ChatConversation::class, 'chat_participants', 'user_id', 'conversation_id')
            ->withPivot('last_read_at', 'is_muted')
            ->withTimestamps();
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
     * Get state-specific settlement rates for carriers
     */
    public function carrierStates()
    {
        return $this->hasMany(AgentCarrierState::class);
    }

    /**
     * Get sticky notes for this user
     */
    public function stickyNotes()
    {
        return $this->hasMany(StickyNote::class);
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

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Create or update employee record with MIS=Yes when user is created
        // CEO users are excluded from EMS as they are above the system
        static::created(function ($user) {
            // Skip EMS entry for CEO users
            if ($user->hasRole('CEO')) {
                \Log::info('Skipping EMS entry for CEO user', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                return;
            }

            $employee = \App\Models\Employee::where('email', $user->email)->first();
            if ($employee) {
                // Update existing employee to set MIS=Yes
                $employee->update([
                    'mis' => 'Yes',
                    'status' => 'Active',
                ]);
            } else {
                // Create new employee record
                \App\Models\Employee::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'contact_info' => '',
                    'emergency_contact' => '',
                    'cnic' => '',
                    'position' => '',
                    'area_of_residence' => '',
                    'status' => 'Active',
                    'mis' => 'Yes',
                    'passport_image' => null,
                    'account_password' => null,
                ]);
            }

            \Log::info('Employee record synchronized on user creation', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        });

        // Mark employee as terminated when user is deleted
        static::deleting(function ($user) {
            \App\Models\Employee::where('email', $user->email)
                ->update([
                    'status' => 'Terminated',
                    'mis' => 'No',
                ]);

            \Log::info('Employee marked as terminated on user deletion', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        });
    }
}