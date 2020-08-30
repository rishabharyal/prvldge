<?php

namespace App\Services;

use App\User;
use App\FollowRequest;
use App\UserRelationships;
use Illuminate\Support\Facades\DB;
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
        if ($haveIAlreadySentRequest) {
            return [
                'success' => true,
                'status' => 'REQUEST_ALREADY_SENT'
            ];
        }

        $userRelationship = $this->getUserRelationToOtherUser($currentUserId, $toBeFriendId);
        $haveIReceivedRequestFromThisUser = $this->hasFirstUserSentRequestToSecond($toBeFriendId, $currentUserId);

        if (!$userRelationship && $haveIReceivedRequestFromThisUser) {
            [$firstId, $secondId] = $this->arrangeUserId($currentUserId, $toBeFriendId);
            $relationship = new UserRelationships();
            $relationship->follower_id = $firstId;
            $relationship->followed_id = $secondId;
            $relationship->has_blocked = 0;
            $relationship->save();

            DB::table('follow_requests')
                ->where('follower_id', $toBeFriendId)
                ->where('followed_id', $currentUserId)
                ->delete();

            return [
                'success' => true,
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
                'success' => true,
                'status' => 'REQUEST_SENT'
            ];

        }

        if ($userRelationship->has_blocked) {
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
}
