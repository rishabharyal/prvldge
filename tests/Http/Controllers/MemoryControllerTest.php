<?php

namespace Http\Controllers;

use App\Memory;
use Illuminate\Support\Facades\Storage;
use TestCase;
use App\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class MemoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    /*
     * User $user
     */
    private $user;
    private $nonFriendUser;
    private $friendUser;


    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->nonFriendUser = factory(User::class)->create();
        $this->friendUser = factory(User::class)->create();
        $this->user->friends()->create(['followed_id' => $this->friendUser->id, 'blocked' => 0]);
        $this->user->createToken();
        Storage::shouldReceive('put')->andReturn();
    }

    public function test_index_returns_MISSING_USER_ID_PARAM_on_not_passing_user_id()
    {
        $this->json('GET', '/memories', [], [
            'Authorization' => 'Bearer ' . $this->user->tokens()->first()->token
        ])->seeJson([
            'success' => false,
            'status' => 'MISSING_USER_ID_PARAM'
        ]);
    }

    public function test_index_returns_USER_NOT_FOUND_on_providing_invalid_id()
    {
        $this->json('GET', '/memories', ['user_id' => 9999], [
            'Authorization' => 'Bearer ' . $this->user->tokens()->first()->token
        ])->seeJson([
            'success' => false,
            'status' => 'USER_NOT_FOUND'
        ]);
    }

    public function test_index_returns_UNAUTHORIZED_ACTION_on_accessing_non_friend_id()
    {
        $this->json('GET', '/memories', ['user_id' => $this->nonFriendUser->id], [
            'Authorization' => 'Bearer ' . $this->user->tokens()->first()->token
        ])->seeJson([
            'success' => false,
            'status' => 'UNAUTHORIZED_ACTION'
        ]);
    }

    public function test_index_returns_success_on_accessing_own_id()
    {
        $this->json('GET', '/memories', ['user_id' => $this->user->id], [
            'Authorization' => 'Bearer ' . $this->user->tokens()->first()->token
        ])->seeJson([
            'success' => true,
            'data' => []
        ]);
    }

    public function test_index_returns_success_on_accessing_friends_id()
    {
        $this->json('GET', '/memories', ['user_id' => $this->friendUser->id], [
            'Authorization' => 'Bearer ' . $this->user->tokens()->first()->token
        ])->seeJson([
            'success' => true,
            'data' => []
        ]);
    }

    public function test_store_requires_a_caption()
    {

        $memories = factory(Memory::class)->make(['caption' => null]);
        $this->post('/memories', $memories->toArray(),
            ['Authorization' => 'Bearer ' . $this->user->tokens()->first()->token]
        )->seeJsonContains(['caption' => ['The caption field is required.']]);
    }
    public function test_store_requires_a_date()
    {

        $memories = factory(Memory::class)->make(['date' => null]);
        $this->post('/memories', $memories->toArray(),
            ['Authorization' => 'Bearer ' . $this->user->tokens()->first()->token]
        )->seeJsonContains(['date' => ['The date field is required.']]);
    }
    public function test_store_requires_a_visibility()
    {

        $memories = factory(Memory::class)->make(['visibility' => null]);
        $this->post('/memories', $memories->toArray(),
            ['Authorization' => 'Bearer ' . $this->user->tokens()->first()->token]
        )->seeJsonContains(['visibility' => ['The visibility field is required.']]);
    }

    public function test_store_returns_success_on_memories_created()
    {
        $memories = factory(Memory::class)->make();
        $this->post('/memories', $memories->toArray(),
            ['Authorization' => 'Bearer ' . $this->user->tokens()->first()->token])->seeStatusCode(201);


    }

}
