<?php

namespace App\Providers;

use App\Services\Policeman\AccessToken;
use App\Traits\NormallyUsedMethods;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    use NormallyUsedMethods;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->viaRequest('api', static function ($request) {
            if ($request->header('Authorization')) {
                $key = explode(' ',$request->header('Authorization'));
                $accessToken = AccessToken::where('token', $key[1])->first();
                if (!$accessToken) {
                    return null; // return unauthorized
                }
                return $accessToken->user;
            }
        });

        // Gates here

        $this->enableGatesAndPolicies();
    }

    /**
     * All the Gates and Policies are available here
     */
    private function enableGatesAndPolicies(): void
    {
        Gate::any(['update-memory', 'delete-memory'], static function($user, $post) {
            return $user->id === $post->user_id;
        });

        Gate::define('list-memories', function($user, $secondUser) {
            if ($secondUser->id === $user->id) {
                return true;
            }

            $hasViewerFollowedAuthor = $this->hasOneUserFollowedTheOtherUser($user->id, $secondUser->id);

            if ($hasViewerFollowedAuthor) {
                return true;
            }

            return false;

        });

        Gate::any(['see-memory', 'comment-on-memory', 'like-memory', 'react-on-memory'], function($user, $post) {
            // The code needs to be rechecked,,
            // To see private post, you should be yourself
            // To see other's posts, you should be friends.

            // The case when authenticated user is the author of post
            if ($post->user_id === $user->id) {
                return true;
            }

            // If visibility is 0, then no other person can see it
            if (!$post->visibility) {
                return false;
            }

            // This means the viewer has followed the post's author, at least once
            $hasViewerFollowedAuthor = $this->hasOneUserFollowedTheOtherUser($user->id, $post->user_id);

            if ($hasViewerFollowedAuthor) {
                return true;
            }

            return false;

        });
    }
}
