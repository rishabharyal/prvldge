<?php

namespace Http\Controllers;

use App\Services\Policeman\AccessToken;
use Illuminate\Support\Facades\Gate;
use TestCase;
use Illuminate\Support\Facades\Storage;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use App\Memory;
use App\User;

class MemoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    /*
     * User $user
     */
    private $user;
    private $nonFriendUser;
    private $friendUser;
    private $headers;
    private $headersWithAuthorization;


    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->nonFriendUser = factory(User::class)->create();
        $this->friendUser = factory(User::class)->create();
        $this->user->friends()->create(['followed_id' => $this->friendUser->id, 'blocked' => 0]);
        $this->headers = [
            'HTTP_USER_AGENT' => 'Memory Test'
        ];
        $this->headersWithAuthorization = [
            'HTTP_USER_AGENT' => 'Memory Test',
            'Authorization' => 'Bearer ' . $this->user->createToken()
        ];
        Storage::shouldReceive('put')->andReturn();
    }

    public function test_index_returns_MISSING_USER_ID_PARAM_on_not_passing_user_id()
    {
        $this->json('GET', '/memories', [], $this->headersWithAuthorization)
            ->seeJson([
                'success' => false,
                'status' => 'MISSING_USER_ID_PARAM'
            ]);
    }

    public function test_index_returns_USER_NOT_FOUND_on_providing_invalid_id()
    {
        $this->json('GET', '/memories', ['user_id' => 9999], $this->headersWithAuthorization)
            ->seeJson([
                'success' => false,
                'status' => 'USER_NOT_FOUND'
            ]);
    }

    public function test_index_returns_UNAUTHORIZED_ACTION_on_accessing_non_friend_id()
    {
        $this->json('GET', '/memories', ['user_id' => $this->nonFriendUser->id], $this->headersWithAuthorization)
            ->seeJson([
                'success' => false,
                'status' => 'UNAUTHORIZED_ACTION'
            ]);
    }

    public function test_index_returns_success_on_accessing_own_id()
    {
        $this->json('GET', '/memories', ['user_id' => $this->user->id], $this->headersWithAuthorization)
            ->seeJson([
                'success' => true,
                'data' => []
            ]);
    }

    public function test_index_returns_success_on_accessing_friends_id()
    {
        $this->json('GET', '/memories', ['user_id' => $this->friendUser->id], $this->headersWithAuthorization)
            ->seeJson([
                'success' => true,
                'data' => []
            ]);
    }

    public function test_store_requires_a_caption(): void
    {
        $memory = factory(Memory::class)->make(['caption' => null]);

        $this->post('/memories', $memory->toArray(), $this->headersWithAuthorization)
            ->seeJsonContains(['caption' => ['The caption field is required.']]);
    }

    public function test_store_requires_a_visibility(): void
    {
        $memory = factory(Memory::class)->make(['visibility' => null]);

        $this->post('/memories', $memory->toArray(), $this->headersWithAuthorization)
            ->seeJsonContains(['visibility' => ['The visibility field is required.']]);
    }

    public function test_store_requires_a_photo(): void
    {
        $memory = factory(Memory::class)->make(['photo' => null]);
        $this->post('/memories', $memory->toArray(), $this->headersWithAuthorization)
            ->seeJsonContains(['photo' => ['The photo field is required.']]);
    }

//     public function test_store_returns_success_on_memories_created()
//     {
//          $memory = factory(Memory::class)->make([
//              'caption' => 'This is a small test caption',
//              'visibility' => 1
//          ])->toArray();
//          $memory['photo'] = UploadedFile::fake()->image('memory_attachment.jpg');
////            dd($memory);
//          $resp = $this->json('POST', '/memories', $memory, $this->headersWithAuthorization)
//              ->response->content();
//          dd($resp);
//     }

    public function test_destory_returns_unauthorized_on_memories_delete_without_permission()
    {
        $memory = factory(Memory::class)->make()->toArray();
        $this->delete('/memories', $memory, $this->headersWithAuthorization)
            ->seeJson(['401'=>'UNAUTHORIZED_ACTION']);
    }

    public function test_destroy_returns_success_on_memories_deleted()
    {
        $memory = factory(Memory::class)->create()->toArray();
        $res = $this->json('delete','/memories', $memory, $this->headersWithAuthorization)
            ->response->getContent();
        dd($res);
    }
    public function test_store_returns_success_on_memories_deleted()
    {
        $memory = factory(Memory::class)->make()->toArray();
        $this->delete('/memories', $memory, $this->headersWithAuthorization)->seeJson(['success']);
    }

}
