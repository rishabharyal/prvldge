<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\Route;

//Route::get('/', function() {
//    return view('welcome');
//});

$router->get('/', function () use ($router) {
    return view('welcome');
});


$router->group(['middleware' => 'api-header', 'prefix' => 'api'], function($app){
    //Login, register and phone number check
    $app->post('/login', 'AuthController@login');
    $app->post('/register', 'AuthController@register');
    $app->post('/phone/check-availability', 'ValidationController@checkPhoneAvailability');

    $app->group(['middleware' => 'auth'], static function($router) {

        //User Info
        $router->get('/me', 'AuthController@user');

        //Otp
        $router->post('/phone/send-otp', 'OTPController@send');
        $router->post('/phone/verify-otp', 'OTPController@verify');

        //Friends
        $router->post('/friend/add', 'FriendController@add');

        //Memory
        $router->get('memories', 'MemoryController@index');
        $router->get('memories/{id}', 'MemoryController@show');
        $router->post('memories', 'MemoryController@store');
        $router->delete('memories/{id}', 'MemoryController@destroy');
        $router->put('memories/{id}', 'MemoryController@update');

        //Feed
        $router->get('feed', 'FeedController@index');
        $router->get('feed/get-dates', 'MemoryController@getFeedDates');

        //Replies
        $router->get('/replies', 'MemoryRepliesController@index');
        $router->post('/replies', 'MemoryRepliesController@store');
        $router->post('/replies{id}', 'MemoryRepliesController@update');
        $router->delete('/replies{id}', 'MemoryRepliesController@delete');
    });
});

