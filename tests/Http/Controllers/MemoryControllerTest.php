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
            'HTTP_USER_AGENT' => 'Memory App'
        ];
        $this->headersWithAuthorization = [
            'HTTP_USER_AGENT' => 'Memory App',
            'Authorization' => 'Bearer ' . $this->user->createToken()
        ];
        Storage::shouldReceive('put')->andReturn();
    }

    public function test_index_returns_MISSING_USER_ID_PARAM_on_not_passing_user_id()
    {
        $this->json('GET', '/api/memories', [], $this->headersWithAuthorization)
            ->seeJson([
                'success' => false,
                'status' => 'MISSING_USER_ID_PARAM'
            ]);
    }

    public function test_index_returns_USER_NOT_FOUND_on_providing_invalid_id()
    {
        $this->json('GET', '/api/memories', ['user_id' => 9999], $this->headersWithAuthorization)
            ->seeJson([
                'success' => false,
                'status' => 'USER_NOT_FOUND'
            ]);
    }

    public function test_index_returns_UNAUTHORIZED_ACTION_on_accessing_non_friend_id()
    {
        $this->json('GET', '/api/memories', ['user_id' => $this->nonFriendUser->id], $this->headersWithAuthorization)
            ->seeJson([
                'success' => false,
                'status' => 'UNAUTHORIZED_ACTION'
            ]);
    }

    public function test_index_returns_success_on_accessing_own_id()
    {
        $this->json('GET', '/api/memories', ['user_id' => $this->user->id], $this->headersWithAuthorization)
            ->seeJson([
                'success' => true,
                'data' => []
            ]);
    }

    public function test_index_returns_success_on_accessing_friends_id()
    {
        $this->json('GET', '/api/memories', ['user_id' => $this->friendUser->id], $this->headersWithAuthorization)
            ->seeJson([
                'success' => true,
                'data' => []
            ]);
    }

    public function test_store_requires_a_caption(): void
    {
        $memory = factory(Memory::class)->make(['caption' => null])->toArray();
        $memory['photo'] = UploadedFile::fake()->image('memory_attachment.jpg');

        $this->post('/api/memories', $memory, $this->headersWithAuthorization)
            ->seeJsonContains(['caption' => ['The caption field is required.']]);
    }

    public function test_store_requires_a_visibility(): void
    {
        $memory = factory(Memory::class)->make(['visibility' => null])->toArray();
        $memory['photo'] = UploadedFile::fake()->image('memory_attachment.jpg');
        $this->json('POST', '/api/memories', $memory, $this->headersWithAuthorization)
            ->seeJsonContains(['visibility' => ['The visibility field is required.']]);
    }

//    public function test_store_requires_a_type(): void
//    {
//        $memory = factory(Memory::class)->make(['type' => null])->toArray();
//        $memory['photo'] = UploadedFile::fake()->image('memory_attachment.jpg');
//        $this->json('POST', '/api/memories', $memory, $this->headersWithAuthorization)
//            ->seeJsonContains(['type' => ['The type field is required.']]);
//    }

    public function test_store_requires_a_photo(): void
    {
        $memory = factory(Memory::class)->make(['photo' => null]);
        $this->post('/api/memories', $memory->toArray(), $this->headersWithAuthorization)
            ->seeJsonContains(['photo' => ['The photo field is required.']]);
    }

    public function test_store_returns_success_on_memories_created()
    {
         $memory = factory(Memory::class)->make()->toArray();
         $memory['photo'] = UploadedFile::fake()->image('memory_attachment.jpg');
         $resp = $this->post('/api/memories', $memory, $this->headersWithAuthorization)
            ->seeJson([
                'user_id' => $this->user->id,
//                'type' => $memory['type'],
                'caption' => $memory['caption']
            ]);
    }

    public function test_destory_returns_unauthorized_on_memories_delete_without_permission()
    {
        $memory = factory(Memory::class)->create([
            'user_id' => $this->nonFriendUser->id
        ]);
        $this->delete('/api/memories/' . $memory->id, [], $this->headersWithAuthorization)
            ->seeJson([
                'success' => 'false',
                'code' => '401',
                'action_code' => 'UNAUTHORIZED_ACTION'
            ]);
    }

    public function test_destroy_returns_success_on_memories_deleted()
    {
        $memory = factory(Memory::class)->create([
            'user_id' => $this->user->id
        ]);
        $this->delete('/api/memories/' . $memory->id, [], $this->headersWithAuthorization)->seeJson(
            ['success' => true]
        );
    }

    public function test_show_returns_unauthorized_without_permission()
    {
        $memory = factory(Memory::class)->create([
            'user_id' => $this->nonFriendUser->id
        ]);
        $this->json('GET', '/api/memories/'. $memory->id, [], $this->headersWithAuthorization)
            ->seeJson([
                'status' => 'UNAUTHORIZED_ACTION'
            ]);
    }

    public function test_show_returns_success()
    {
        $memory = factory(Memory::class)->create([
            'user_id' => $this->user->id
        ]);
        $this->json('GET', '/api/memories/'. $memory->id, [], $this->headersWithAuthorization)
            ->seeStatusCode(200)
            ->seeJson([
                'success' => true
            ]);
    }

    public function test_update_returns_unauthorized_without_permission()
    {
        $memory = factory(Memory::class)->create([
            'user_id' => $this->nonFriendUser->id
        ]);
        $this->put('/api/memories/'. $memory->id, ['caption' => 'New Updated Caption!'], $this->headersWithAuthorization)
            ->seeJson([
                'status' => 'UNAUTHORIZED_ACTION'
            ]);
    }

    public function test_update_returns_success() {
        $memory = factory(Memory::class)->create([
            'user_id' => $this->user->id
        ]);

        $this->put('/api/memories/'. $memory->id, ['caption' => 'Newly Updated Caption!'], $this->headersWithAuthorization)
            ->seeJson([
                'success' => true
            ]);
    }

}
