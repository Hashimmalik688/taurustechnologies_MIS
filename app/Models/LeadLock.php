<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadLock extends Model
{
    public $timestamps = false;

    protected $fillable = ['lead_id', 'user_id', 'locked_at'];

    protected $casts = ['locked_at' => 'datetime'];

    /** Lock expires after this many minutes with no refresh. */
    const TTL_MINUTES = 15;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
