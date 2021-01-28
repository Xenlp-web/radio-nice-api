<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//Auth
Route::post('/register', 'App\Http\Controllers\AuthController@register');
Route::post('/login', 'App\Http\Controllers\AuthController@login');
Route::middleware('auth:sanctum')->post('/logout', 'App\Http\Controllers\AuthController@logout');


//User
Route::middleware('auth:sanctum')->get('/user/current', 'App\Http\Controllers\UserController@getCurrent');
Route::middleware(['auth:sanctum', 'user.admin'])->get('/user/{userId?}', 'App\Http\Controllers\UserController@get');
Route::middleware('auth:sanctum')->post('/user/current/edit', 'App\Http\Controllers\UserController@edit');


//Advertisement
Route::get('/advert/artist/{bannerId?}', 'App\Http\Controllers\Advertisement\ArtistAdvertController@getAll');
Route::middleware(['auth:sanctum', 'user.admin'])->post('/advert/artist/save', 'App\Http\Controllers\Advertisement\ArtistAdvertController@save');
Route::middleware(['auth:sanctum', 'user.admin'])->delete('/advert/artist/delete/{bannerId}', 'App\Http\Controllers\Advertisement\ArtistAdvertController@delete');


//Stream
Route::get('/stream/{streamId?}', 'App\Http\Controllers\StreamController@get');
Route::get('/stream/url/{streamId}', 'App\Http\Controllers\StreamController@getStreamUrl');
Route::get('/stream/track/{streamId}', 'App\Http\Controllers\StreamController@getCurrentTrack');
Route::middleware(['auth:sanctum', 'user.admin'])->post('/stream/save', 'App\Http\Controllers\StreamController@save');
Route::middleware(['auth:sanctum', 'user.admin'])->post('stream/edit/{streamId}', 'App\Http\Controllers\StreamController@edit');
Route::middleware(['auth:sanctum', 'user.admin'])->delete('/stream/delete/{streamId}', 'App\Http\Controllers\StreamController@delete');


//Errors
Route::get('errorUnauthorized', function() {
    return response()->json(['message' => 'Не авторизован', 'status' => 'error'], 401);
})->name('unauthorized');
