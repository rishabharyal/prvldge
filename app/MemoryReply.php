<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class MemoryReply extends Model
{
    protected $fillable = [
        'user_id',
        'memory_id',
        'type',
        'memory_reply_suggestion_id',
        'comment'
    ];

    protected $table = 'memory_replies';

}
