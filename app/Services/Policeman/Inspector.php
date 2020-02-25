<?php

namespace App\Services\Policeman;

use App\Services\Policeman\Policeman;
use Detect;

trait Inspector
{
	public function createToken($permissions = ['*'])
	{
        return $this->tokens()->create([
            'token' => (new Token($this->getKey()))->generateToken(),
            'ip_address' => Detect::ip(),
            'device_name' => Detect::platform()['device_name'] ?? 'N/A',
            'metadata' => [
                'platform' => Detect::platform(),
                'browser' => Detect::browser()
            ],
            'permissions' => $permissions,
            'user_id' => $this->getKey()
        ]);
	}

	public function tokens()
	{
		return $this->hasMany(Policeman::$accessTokenModel, 'user_id');
	}

}
