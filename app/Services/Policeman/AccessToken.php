<?php

namespace App\Services\Policeman;

use App\User;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model {

	protected $table = 'user_access_tokens';

	protected $fillable = [
		'token',
		'user_id',
		'ip_address',
		'device_name',
		'metadata',
		'permissions'
	];

	protected $casts = [
		'permissions' => 'array',
        'metadata' => 'array'
	];

    protected $primaryKey = 'token';
    protected $keyType = 'string';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
	    return $this->belongsTo(User::class);
    }

    /**
     * @param $action
     * @return bool
     */
    public function can($action): bool
    {
        return in_array('*', $this->permissions, false) ||
               array_key_exists($action, array_flip($this->permissions));
    }

    /**
     * @param $ability
     * @return bool
     */
    public function cant($ability): bool
    {
        return !$this->can($ability);
    }

}
