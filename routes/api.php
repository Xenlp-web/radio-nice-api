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
Route::post('/password/forgot', 'App\Http\Controllers\AuthController@forgotPassword');
Route::get('/password/reset', 'App\Http\Controllers\AuthController@passwordReset')->name('password.reset');


//Auth Social
Route::middleware('web')->group(function() {
    Route::get('/auth/social/{driver}', 'App\Http\Controllers\AuthSocial@init');
    Route::get('/auth/social/{driver}/token', 'App\Http\Controllers\AuthSocial@getToken');
    Route::post('/auth/social/{driver}/execute', 'App\Http\Controllers\AuthSocial@execute');
    Route::middleware('auth:sanctum')->post('/auth/social/{driver}/link-account', 'App\Http\Controllers\AuthSocial@linkAccount');
});


//User
Route::middleware('auth:sanctum')->get('/user/current', 'App\Http\Controllers\UserController@getCurrent');
Route::middleware('auth:sanctum')->get('/user/current/socials', 'App\Http\Controllers\UserController@getSocials');
Route::middleware(['auth:sanctum', 'user.admin'])->get('/user/{userId?}', 'App\Http\Controllers\UserController@get');
Route::middleware('auth:sanctum')->post('/user/current/edit', 'App\Http\Controllers\UserController@edit');


//Advertisement
Route::get('/banner/artist/{bannerId?}', 'App\Http\Controllers\Advertisement\ArtistAdvertController@getAll');
Route::middleware(['auth:sanctum', 'user.admin'])->post('/banner/artist/save', 'App\Http\Controllers\Advertisement\ArtistAdvertController@save');
Route::middleware(['auth:sanctum', 'user.admin'])->delete('/banner/artist/delete/{bannerId}', 'App\Http\Controllers\Advertisement\ArtistAdvertController@delete');
Route::middleware(['auth:sanctum', 'user.admin'])->post('/banner/artist/edit/{bannerId}', 'App\Http\Controllers\Advertisement\ArtistAdvertController@edit');


//Stream
Route::get('/stream/{streamId?}', 'App\Http\Controllers\StreamController@get');
Route::get('/stream/url/{streamId}', 'App\Http\Controllers\StreamController@getStreamUrl');
Route::middleware(['auth:sanctum', 'premium'])->get('/stream/track/{streamId}', 'App\Http\Controllers\StreamController@getCurrentTrack');
Route::get('/stream/history/{streamId}', 'App\Http\Controllers\StreamController@getLastTracks');
Route::get('/listeners/geo', 'App\Http\Controllers\StreamController@getListenersGeo');
Route::middleware(['auth:sanctum', 'premium'])->post('/stream/track/like/{trackId}', 'App\Http\Controllers\StreamController@trackVoteUp');
Route::middleware(['auth:sanctum', 'premium'])->post('/stream/track/dislike/{trackId}', 'App\Http\Controllers\StreamController@trackVoteDown');
Route::middleware(['auth:sanctum', 'user.admin'])->post('/stream/save', 'App\Http\Controllers\StreamController@save');
Route::middleware(['auth:sanctum', 'user.admin'])->post('/stream/edit/{streamId}', 'App\Http\Controllers\StreamController@edit');
Route::middleware(['auth:sanctum', 'user.admin'])->delete('/stream/delete/{streamId}', 'App\Http\Controllers\StreamController@delete');


//Subscriptions
Route::get('/subscription/get', 'App\Http\Controllers\Shop\PremiumSubscriptionController@get');
Route::middleware(['auth:sanctum'])->get('/subscription/purchase/{subscriptionId}', 'App\Http\Controllers\Shop\PremiumSubscriptionController@purchase');
Route::post('/subscription/purchase/success', 'App\Http\Controllers\Shop\PremiumSubscriptionController@handleSuccessPurchase');
Route::middleware(['auth:sanctum', 'user.admin'])->post('/subscription/save', 'App\Http\Controllers\Shop\PremiumSubscriptionController@save');
Route::middleware(['auth:sanctum', 'user.admin'])->post('/subscription/edit/{subscriptionId}', 'App\Http\Controllers\Shop\PremiumSubscriptionController@edit');
Route::middleware(['auth:sanctum', 'user.admin'])->delete('/subscription/delete/{subscriptionId}', 'App\Http\Controllers\Shop\PremiumSubscriptionController@delete');


//Emails
Route::get('/email/verify/{id}', 'App\Http\Controllers\VerificationController@verify')->name('verification.verify');
Route::get('/email/resend', 'App\Http\Controllers\VerificationController@resend')->name('verification.resend');
Route::post('/email/feedback', 'App\Http\Controllers\MailController@sendFeedback');
Route::post('/email/offer/track', 'App\Http\Controllers\MailController@offerTrack');


//Time Tracker
Route::post('/time-tracker/update', 'App\Http\Controllers\TimeTrackController@update');


//Errors
Route::get('errorUnauthorized', function() {
    return response()->json(['message' => 'Не авторизован', 'status' => 'error'], 401);
})->name('unauthorized');
