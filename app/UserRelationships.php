<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRelationships extends Model
{
	protected $table = 'user_relationships';

	protected $fillable = [
		'follower_id',
		'followed_id',
		'has_blocked'
	];

	public $timestamps = false;
}
