<?php
namespace App\Traits;

use App\UserRelationships;
use Illuminate\Http\Request;

trait NormallyUsedMethods {

    /**
     * @param null $agent
     * @return bool
     * // here...
     */
    private function isRequestAllowedToProceed($agent = null): bool
    {
        if ($agent === 'Memory Test' || $agent === 'Memory App') {
            return true;
        }

        return false;
    }


    /**
     * @param $firstId
     * @param $secondId
     * @return bool
     * Returns if the user with first id has followed the second user with second id
     */
    public function hasOneUserFollowedTheOtherUser($firstId, $secondId): bool
    {
        $relation = UserRelationships::where('follower_id', $firstId)->where('followed_id', $secondId)->first();
        if ($relation) {
            return true;
        }

        return false;
    }

    /**
     * @param $firstId
     * @param $secondId
     * @return bool
     * Returns if the user with first id has blocked the second user with second id
     */
    public function hasOneUserBlockedTheOtherUser($firstId, $secondId): bool
    {
        $relation = UserRelationships::where('follower_id', $firstId)->where('followed_id', $secondId)->where('has_blocked', 1)->first();
        if ($relation) {
            return true;
        }

        return false;
    }

    public function getUserVisibility($userId)
    {

    }
}
