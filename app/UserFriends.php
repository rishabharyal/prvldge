<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFriends extends Model
{

    protected $table = 'user_friends';

    protected $casts = [
        'friends' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'friends',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the post's author instance
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
