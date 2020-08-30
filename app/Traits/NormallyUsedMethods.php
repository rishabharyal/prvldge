<?php
namespace App\Traits;

use App\UserRelationships;
use App\FollowRequest;
use Illuminate\Http\Request;

trait NormallyUsedMethods {


    /**
     * @param $firstId
     * @param $secondId
     * @return bool
     * Returns UserRelationships instance if they are already related
     */
    public function getUserRelationToOtherUser($firstId, $secondId): ?UserRelationships
    {
        list($firstId, $secondId) = $this->arrangeUserId($firstId, $secondId);
        return UserRelationships::where('follower_id', $firstId)
            ->where('followed_id', $secondId)
            ->first();
    }

    /**
     * @param $firstId
     * @param $secondId
     * @return bool
     * Returns true if both of the friends have added each other
     */
    public function hasOneUserFollowedTheOtherUser($firstId, $secondId): bool
    {
        list($firstId, $secondId) = $this->arrangeUserId($firstId, $secondId);
        $relation = UserRelationships::where('follower_id', $firstId)
            ->where('followed_id', $secondId)
            ->where('has_blocked', false)
            ->first();

        if ($relation) {
            return true;
        }

        return false;
    }

    /**
     * @param $firstId
     * @param $secondId
     * @return bool
     * Returns true if there is already a request from one of the accounts...
     */
    public function hasFirstUserSentRequestToSecond($firstId, $secondId): bool
    {
        $relation = FollowRequest::where('follower_id', $firstId)
            ->where('followed_id', $secondId)
            ->first();

        if ($relation) {
            return true;
        }

        return false;
    }

    /**
     * @param $firstId
     * @param $secondId
     * @return bool
     * Returns true if one of the users have blocked the other...
     */
    public function hasOneUserBlockedTheOtherUser($firstId, $secondId): bool
    {
        list($firstId, $secondId) = $this->arrangeUserId($firstId, $secondId);

        $relation = UserRelationships::where('follower_id', $firstId)
            ->where('followed_id', $secondId)
            ->where('has_blocked', 1)
            ->first();

        if ($relation) {
            return true;
        }

        return false;
    }

    public function arrangeUserId($firstId, $secondId): array
    {
        if ($firstId > $secondId) {
            return [$secondId, $firstId];
        }

        return [$firstId, $secondId];
    }
}
