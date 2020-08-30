<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowRequest extends Model
{

    protected $table = 'follow_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'follower_id',
        'followed_id',
        'is_seen',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the post's author instance
     */
    // public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }
}
