<?php

namespace Http\Controllers;

use App\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use TestCase;

class AuthControllerTest extends TestCase
{

    use DatabaseMigrations;

    private string $phoneNumber;
    private string $username;
    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->headers = [
            'HTTP_USER_AGENT' => 'Memory Test'
        ];
        $this->phoneNumber = '9865912' . rand(100, 999);
        $this->username = uniqid('user_', true);
        factory(User::class)->create([
            'phone_number' => '977 ' . $this->phoneNumber,
            'username' => $this->username
        ]);
    }

    /**
     * On request to the /check phone, is available or not, we send the invalid prefix code to
     * fail the validation
     */
    public function test_checkPhone_returns_INVALID_PREFIX_CODE_on_invaid_prefix_code(): void
    {
        $this->json('GET', '/api/phone/check-availability', [
            'data' => [
                'prefix_code' => '989',
                'phone_number' => '9865012999'
            ]
        ], $this->headers)->seeJson([
            'status' => 'INVALID_PREFIX_CODE',
            'success' => false
        ]);
    }

    /**
     * On request to the /check phone, is available or not, we send the invalid phone number to
     * fail the validation
     */
    public function test_checkPhone_returns_INVALID_PHONE_NUMBER_on_invaid_phone_number(): void
    {
        $this->json('GET', '/api/phone/check-availability', [
            'prefix_code' => '977',
            'phone_number' => '986501299'
        ], $this->headers)->seeJson([
            'status' => 'INVALID_PHONE_NUMBER',
            'success' => false
        ]);
    }

    /**
     * On request to the /check phone, is available or not, we send the existing phone number to
     * fail the validation
     */
    public function test_checkPhone_returns_PHONE_NUMBER_ALREADY_EXISTS_on_existing_phone_number(): void
    {
        $this->json('GET', '/api/phone/check-availability', [
            'prefix_code' => '977',
            'phone_number' => $this->phoneNumber
        ], $this->headers)->seeJson([
            'status' => 'PHONE_NUMBER_ALREADY_EXISTS',
            'success' => false
        ]);
    }

    /**
     * On request to the /check phone, is available or not, we send the unqiue phone number to
     * success the validation
     */
    public function test_checkPhone_returns_AVAILABLE_on_new_phone_number(): void
    {
        $this->json('GET', '/api/phone/check-availability', [
            'prefix_code' => '977',
            'phone_number' => '1234567' . rand(100, 999)
        ], $this->headers)->seeJson([
            'status' => 'AVAILABLE',
            'success' => true
        ]);
    }

    /**
     * On request to the /register url, we pass default user agent, i.e. Symphony, but its not accepted,
     * so returns NOT_ALLOWED response
     */
    public function test_register_returns_NOT_ALLOWED_when_passed_with_invalid_header(): void {
        $this->json('POST', '/api/register', [
            'username' => uniqid('user_', true),
            'password' => 'password',
            'name' => 'Miniyan Gadha',
            'birthday' => date('Y-m-d'),
            'gender' => 'm'
        ])->seeJson([
            'status' => 'NOT_ALLOWED',
            'success' => false
        ]);
    }

    /**
     * On request to the /register url, we pass only name, so we expect we receive,
     * validation failed response
     */
    public function test_register_returns_validation_error_when_passed_with_invalid_data(): void {
        $this->json('POST', '/api/register', [
            'name' => 'Miniyan Gadha',
        ], $this->headers)->seeJson([
            'success' => false,
            'status' => 'VALIDATION_FAILED'
        ]);
    }

    /**
     * On request to the /register url, all values correct, except for username,
     * so returns invalid username format response
     */
    public function test_register_returns_validation_error_when_passed_with_invalid_username(): void
    {
        $this->json('POST', '/api/register', [
            'username' => uniqid('user!', true),
            'password' => 'password',
            'name' => 'Miniyan Gadha',
            'birthday' => date('Y-m-d'),
            'gender' => 'm'
        ], $this->headers)->seeJson([
            'success' => false,
            'status' => 'VALIDATION_FAILED',
            'data' => [
                'username' => ['The username format is invalid.']
            ]
        ]);
    }

    /**
     * On request to the /register url, all values correct, so new user should be created,
     * and respond with token and basic user info
     */
    public function test_register_returns_success_when_passed_with_all_valid_fields(): void
    {
        $username = uniqid('user_', true);
        $this->json('POST', '/api/register', [
            'username' => $username,
            'password' => 'password',
            'name' => 'Miniyan Gadha',
            'birthday' => date('Y-m-d'),
            'gender' => 'm'
        ], $this->headers)->seeJson([
            'success' => true,
            'name' => 'Miniyan Gadha',
            'new' => true,
            'username' => $username
        ]);
    }

    /**
     * On request to the /login url, we pass default user agent, i.e. Symphony, but its not accepted,
     * so returns NOT_ALLOWED response
     */
    public function test_login_returns_NOT_ALLOWED_when_passed_with_invalid_header(): void {
        $this->json('POST', '/api/register', [
            'username' => uniqid('user_', true),
            'password' => 'password',
        ])->seeJson([
            'status' => 'NOT_ALLOWED',
            'success' => false
        ]);
    }

    /**
     * On request to the /login url, all values correct, so user should be logged in,
     * and respond with token and basic user info
     */
    public function test_login_returns_success_when_passed_with_all_valid_fields(): void
    {
        $this->json('POST', '/api/login', [
            'username' => $this->username,
            'password' => 'password',
        ], $this->headers)->seeJson([
            'success' => true
        ]);
    }

    /**
     * On request to the /login url, all values correct, so new user should be created,
     * and respond with token and basic user info
     */
    public function test_login_returns_INVALID_USERNAME_when_passed_with_invalid_username(): void
    {
        $this->json('POST', '/api/login', [
            'username' => $this->username . '_fails',
            'password' => 'password',
        ], $this->headers)->seeJson([
            'success' => false,
            'status' => 'INVALID_USERNAME'
        ]);
    }

    /**
     * On request to the /login url, all values correct, so new user should be created,
     * and respond with token and basic user info
     */
    public function test_login_returns_INVALID_PASSWORD_when_passed_with_invalid_password(): void
    {
        $username = uniqid('user_', true);
        $this->json('POST', '/api/login', [
            'username' => $this->username,
            'password' => 'wrong-password',
        ], $this->headers)->seeJson([
            'success' => false,
            'status' => 'INVALID_PASSWORD'
        ]);
    }
}
