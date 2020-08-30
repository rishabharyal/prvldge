<?php

namespace App\Services;

use App\User;
use App\FollowRequest;
use App\UserRelationships;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\NormallyUsedMethods;

class Friend {

	use NormallyUsedMethods;

	public function add(array $params): array
    {
        $validator = Validator::make($params, [
            'username' => 'required|regex:/^[a-zA-Z0-9._]+$/',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'status' => 'INVALID_USERNAME'
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

        $currentUserId = Auth::id();
        $toBeFriendId = $user->id;

        $haveIAlreadySentRequest = $this->hasFirstUserSentRequestToSecond($currentUserId, $toBeFriendId);
        if ($haveISentRequest) {
            return [
                'status' => success,
                'status' => 'REQUEST_ALREADY_SENT'
            ];
        }

        $userRelationship = $this->getUserRelationToOtherUser($currentUserId, $toBeFriendId);
        $haveIReceivedRequestFromThisUser = $this->hasFirstUserSentRequestToSecond($toBeFriendId, $currentUserId);

        if (!$userRelationship && $haveIReceivedRequestFromThisUser) {
            [$firstId, $secondId] = $this->arrangeUserId($currentUserId, $toBeFriendId);
            $relationship = new UserRelationships();
            $relationship->follower_id = $firstId;
            $relationship->secondId = $secondId;
            $relationship->has_blocked = 0;
            $relationship->save();

            return [
                'status' => success,
                'status' => 'USER_ADDED_AS_FRIEND'
            ];
        }

        if (!$userRelationship && !$haveIReceivedRequestFromThisUser) {
            $followRequest = new FollowRequest();
            $followRequest->is_seen = 0;
            $followRequest->follower_id = $currentUserId;
            $followRequest->followed_id = $toBeFriendId;
            $followRequest->save();

            return [
                'status' => success,
                'status' => 'REQUEST_SENT'
            ];

        }

        if ($userRelationship->is_blocked) {
            return [
                'status' => 'INVALID_USERNAME',
                'success' => false
            ];
        }

        return [
            'status' => 'ALREADY_FRIENDS',
            'success' => true
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
