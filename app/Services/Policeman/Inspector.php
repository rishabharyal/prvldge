<?php

namespace App\Services\Policeman;

use App\Services\Policeman\Policeman;
use Detect;

trait Inspector
{
	public function createToken($permissions = ['*']): string
    {
	    $token = (new Token($this->getKey()))->generateToken();
        $this->tokens()->create([
            'token' => $token,
            'ip_address' => '127.0.0.1',
            'device_name' => 'N/A',
            'metadata' => [
                'platform' => 'Test',
                'browser' => 'Symphony'
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
