<?php


namespace Http\Controllers;


use App\Memory;
use App\MemoryReply;
use App\User;

class MemoryRepliesControllerTest extends \TestCase
{
    private $user;
    private $memory;
    private $reply;
    private $headers;
    private $headersWithAuthorization;
    public function setUp():void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->memory = factory(Memory::class)->create();
        $this->reply =  factory(MemoryReply::class)->create();
        $this->headers = [
            'HTTP_USER_AGENT' => 'Memory App'
        ];
        $this->headersWithAuthorization = [
            'HTTP_USER_AGENT' => 'Memory App',
            'Authorization' => 'Bearer ' . $this->user->createToken()
        ];
    }

    public function test_index_returns_MISSING_USER_ID_PARAM_on_not_passing_user_id()
    {
        $this->json('GET', '/api/replies', [], $this->headersWithAuthorization)
            ->seeJson([
                'success' => false,
                'status' => 'MISSING_USER_ID_PARAM'
            ]);
    }

    public function test_index_returns_replies_on_passing_user_id()
    {
        $this->json('GET','/api/replies',["user_id"=> 1], $this->headersWithAuthorization)
            ->seeJson([
                'status'=> true
            ]);
    }

    public function test_store_returns_MISSING_USER_ID_on_not_passing_id()
    {
        $this->json('POST','/api/replies',[],$this->headersWithAuthorization)
            ->seeJson([
                'success' => false,
                'status' => 'MISSING_USER_ID_PARAM'
            ]);
    }

    public function test_store_requires_memory_id()
    {
        $replies = factory(MemoryReply::class)->make(['memory_id'=>null])->toArray();
        $this->json('POST','api/replies',$replies,$this->headersWithAuthorization)
            ->seeJsonContains(['memory_id'=> ['The memory_id field is required']]);
    }

    public function test_store_requires_type()
    {
        $replies = factory(MemoryReply::class)->make(['type'=>null])->toArray();
        $this->json('POST','api/replies',$replies,$this->headersWithAuthorization)
            ->seeJsonContains(['type'=> ['The type field is required']]);
    }

    public function test_store_requires_comment()
    {
        $replies = factory(MemoryReply::class)->make(['comment'=>null])->toArray();
        $this->json('POST','api/replies',$replies,$this->headersWithAuthorization)
            ->seeJsonContains(['comment'=> ['The comment field is required']]);
    }

}
