<?php

namespace App\Services\Policeman;

use App\Services\Policeman\Policeman;
use Detect;

trait Inspector
{
	public function createToken($permissions = ['*'])
	{
	    $token = (new Token($this->getKey()))->generateToken();
        $this->tokens()->create([
            'token' => $token,
            'ip_address' => Detect::ip(),
            'device_name' => Detect::platform()['device_name'] ?? 'N/A',
            'metadata' => [
                'platform' => Detect::platform(),
                'browser' => Detect::browser()
            ],
            'permissions' => $permissions,
            'user_id' => $this->getKey()
        ]);

        return $token;
	}

	public function tokens()
	{
		return $this->hasMany(Policeman::$accessTokenModel, 'user_id');
	}

}
