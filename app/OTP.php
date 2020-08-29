<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{

    protected $table = 'otp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'otp_hash',
        'sent_times',
        'last_sent_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the user who initiated the OTP
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
