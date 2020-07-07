<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\NormallyUsedMethods;

class Authentication {

	use NormallyUsedMethods;

	public function login(array $params): array
    {
        $validator = Validator::make($params, [
            'username' => 'required|regex:/^[a-zA-Z0-9._]+$/',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'status' => 'VALIDATION_FAILED',
                'data' => $validator->errors()
            ];
        }

        $username = $params['username'];

        $user = User::where('username', $username)->first();

        if (!$user) {
            return [
                'status' => 'INVALID_USERNAME',
                'success' => false
            ];
        }

        $password = $user->password;

        if (!Hash::check($params['password'], $password)) {
            return [
                'status' => 'INVALID_PASSWORD',
                'success' => false
            ];
        }

        $token = $user->createToken();

        return [
            'access_token' => $token,
            'name' => $user->name,
            'username' => $user->username,
            'success' => true
        ];
	}

	public function register(array $params): array
    {
        $validator = Validator::make($params, [
            'username' => 'required|unique:users,username|regex:/^[a-zA-Z0-9._]+$/',
            'password' => 'required|min:6',
            'name' => 'required',
            'birthday' => 'date|required',
            'gender' => 'required|in:m,f,o'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'status' => 'VALIDATION_FAILED',
                'data' => $validator->errors()
            ];
        }

        $params['password'] = Hash::make($params['password']);
        $user = User::create($params);

        return [
            'data' => [
            	'access_token' => $user->createToken(),
	            'name' => $user->name,
	            'username' => $user->username,
	            'new' => true,
            ],
            'success' => true
        ];
	}
}
