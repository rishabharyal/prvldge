<?php

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

Route::get('/', function() {
    return view('welcome');
});

Route::group(['middleware' => 'api-header', 'prefix' => 'api'], static function($router) {
	//Login, register and phone number check
	$router->post('/login', 'AuthController@login');
	$router->post('/register', 'AuthController@register');
	$router->get('/phone/check-availability', 'ValidationController@checkPhoneAvailability');

	$router->group(['middleware' => 'auth'], static function($router) {

		//Memory
	    $router->get('memories', 'MemoryController@index');
	    $router->get('memories/{id}', 'MemoryController@show');
	    $router->post('memories', 'MemoryController@store');
	    $router->delete('memories/{id}', 'MemoryController@destroy');
	    $router->put('memories/{id}', 'MemoryController@update');

	    //Feed
	    $router->get('feed', 'FeedController@index');
	    $router->get('feed/get-dates', 'MemoryController@getFeedDates');
	});
});
