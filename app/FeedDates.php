<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeedDates extends Model
{

    protected $table = 'feed_dates';

    protected $casts = [
        'post_dates' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'post_dates',
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
