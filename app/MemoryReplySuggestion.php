<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class MemoryReplySuggestion extends  Model
{
    protected $fillable = [
        'user_id',
        'title',
        'emoji',
        'keywords',
        'metadata'
    ];
    protected $table = 'memory_reply_suggestions';

}
