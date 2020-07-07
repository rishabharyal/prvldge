<?php

namespace App;

use App\Services\Policeman\Inspector;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, Inspector;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'email',
        'birthday',
        'gender',
        'access_token',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function friendIdList(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(UserFriends::class);
    }

    public function friends(): \Illuminate\Database\Eloquent\Relations\HasMany {
	return $this->hasMany(UserRelationships::class, 'follower_id', 'id');
    }
	    

    public function postDates(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(FeedDates::class);
    }
}
