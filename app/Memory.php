<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Memory extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'caption',
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
