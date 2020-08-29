<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
            'success' => true,
            'status' => 'LOGIN_SUCCESS',
            'data' => [
                'access_token' => $token,
                'name' => $user->name,
                'username' => $user->username
            ]
        ];
	}

    public function getUser() {
        $user = Auth::user();
        return [
            'success' => true,
            'code' => 'USER_INFO',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'birthday' => $user->birthday,
                'gender' => $user->gender,
                'is_phone_verified' => $user->is_phone_verified
            ]
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
