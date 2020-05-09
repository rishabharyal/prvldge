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
    return 'Welcome';
});
Route::post('/login', 'AuthController@login')->middleware('guest');
Route::post('/register', 'AuthController@register')->middleware('guest');
Route::post('/phone/check-availability', 'AuthController@checkPhone');

Route::group(['middleware' => 'auth'], static function($router) {
    $router->get('memories', 'MemoryController@index');
});
