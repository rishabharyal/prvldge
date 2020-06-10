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
Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');
Route::get('/phone/check-availability', 'ValidationController@checkPhoneAvailability');

Route::group(['middleware' => 'auth'], static function($router) {
    $router->get('memories', 'MemoryController@index');
    $router->post('memories', 'MemoryController@store');
    $router->get('feed', 'FeedController@index');
    $router->get('feed/get-dates', 'MemoryController@getFeedDates');
});
