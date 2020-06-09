<?php

namespace Http\Controllers;

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


	public function setUp(): void {
		parent::setUp();
		$this->user = factory(User::class)->create();
		$this->nonFriendUser = factory(User::class)->create();
		$this->friendUser = factory(User::class)->create();
		$this->user->friends()->create(['followed_id' => $this->friendUser->id, 'blocked' => 0]);
		$this->user->createToken();
	}

	public function test_index_returns_MISSING_USER_ID_PARAM_on_not_passing_user_id() {
		$this->json('GET', '/memories', [], [
			'Authorization' =>  'Bearer ' . $this->user->tokens()->first()->token
		])->seeJson([
			'success' => false,
			'status' => 'MISSING_USER_ID_PARAM'
		]);
	}

	public function test_index_returns_USER_NOT_FOUND_on_providing_invalid_id() {
		$this->json('GET', '/memories', ['user_id' => 9999], [
			'Authorization' => 'Bearer ' . $this->user->tokens()->first()->token
		])->seeJson([
			'success' => false,
			'status' => 'USER_NOT_FOUND'
		]);
	}

	public function test_index_returns_UNAUTHORIZED_ACTION_on_accessing_non_friend_id() {
		$this->json('GET', '/memories', ['user_id' => $this->nonFriendUser->id], [
			'Authorization' => 'Bearer ' . $this->user->tokens()->first()->token
		])->seeJson([
			'success' => false,
			'status' => 'UNAUTHORIZED_ACTION'
		]);
	}

	public function test_index_returns_success_on_accessing_own_id() {
		$this->json('GET', '/memories', ['user_id' => $this->user->id], [
			'Authorization' => 'Bearer ' . $this->user->tokens()->first()->token
		])->seeJson([
			'success' => true,
			'data' => []
		]);
	}

	public function test_index_returns_success_on_accessing_friends_id() {
		$this->json('GET', '/memories', ['user_id' => $this->friendUser->id], [
			'Authorization' => 'Bearer '. $this->user->tokens()->first()->token
		])->seeJson([
			'success' => true,
			'data' => []
		]);
	}

}
