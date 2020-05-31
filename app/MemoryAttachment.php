<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemoryAttachment extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'memory_id',
        'file_url',
        'type',
        'storage'
    ];

    protected $table = 'memory_attachments';

    /**
     * Get the post's author instance
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
