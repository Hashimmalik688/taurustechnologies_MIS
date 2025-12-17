<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'state',
        'address',
        'active_states',
        'carriers',
        'role',
        'phone',
        'ssn_last4',
        'dob',
        'gender',
        'join_date',
        'city',
    ];

    protected $casts = [
        'dob' => 'date',
        'join_date' => 'date',
        'active_states' => 'array',  // Automatically cast JSON to array
        'carriers' => 'array',       // Automatically cast JSON to array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActiveStatesStringAttribute()
    {
        if (empty($this->active_states)) {
            return 'None';
        }

        return implode(', ', $this->active_states);
    }

    // Accessor for carriers as a formatted string
    public function getCarriersStringAttribute()
    {
        if (empty($this->carriers)) {
            return 'None';
        }

        return implode(', ', $this->carriers);
    }

    // Mutator to ensure active_states is always an array
    public function setActiveStatesAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['active_states'] = $value;
        } else {
            $this->attributes['active_states'] = json_encode($value ?? []);
        }
    }

    // Mutator to ensure carriers is always an array
    public function setCarriersAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['carriers'] = $value;
        } else {
            $this->attributes['carriers'] = json_encode($value ?? []);
        }
    }
}
