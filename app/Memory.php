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
        'date',
        'caption',
        'type',
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

    public function MemoryReply()
    {
        return $this->hasMany(MemoryReply::class);
    }
}
