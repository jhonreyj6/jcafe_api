<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// Route::domain(config('app.url'))->group(function () {

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
    Route::post('me', 'App\Http\Controllers\AuthController@me');
    Route::post('register', 'App\Http\Controllers\AuthController@register');


    Route::get('/{provider}/redirect', 'App\Http\Controllers\SocialiteController@redirect');
    Route::get('/{provider}/callback', 'App\Http\Controllers\SocialiteController@callback');
});

Route::group(['prefix' => 'dashboard', 'middleware' => 'auth:api'], function ($router) {
    // admin
    Route::get('/', 'App\Http\Controllers\DashboardController@index')->middleware('auth.admin');
});

Route::group(['prefix' => 'users', 'middleware' => 'auth:api'], function ($router) {
    // admin
    Route::get('/', 'App\Http\Controllers\UserController@index')->middleware('auth.admin');
    Route::get('/search', 'App\Http\Controllers\UserController@search');
    Route::post('/', 'App\Http\Controllers\UserController@store')->middleware('auth.admin');
    Route::delete('/', 'App\Http\Controllers\UserController@destroy')->middleware('auth.admin');
    Route::patch('/{id}', 'App\Http\Controllers\UserController@update')->middleware('auth.admin');
    // admin end

    Route::post('/membership', 'App\Http\Controllers\SubscriptionController@store');
    Route::get('/membership/show', 'App\Http\Controllers\SubscriptionController@show');
});

Route::group(['prefix' => 'posts', 'middleware' => 'auth:api'], function ($router) {
    Route::get('/', 'App\Http\Controllers\PostController@index');

    Route::post('/', 'App\Http\Controllers\PostController@store')->middleware('auth.admin');
    Route::patch('/', 'App\Http\Controllers\PostController@update');
    Route::delete('/', 'App\Http\Controllers\PostController@destroy')->middleware('auth.admin');

    Route::get('/show/{id}', 'App\Http\Controllers\PostController@show');
    // post likes
    Route::post('/{id}/like', 'App\Http\Controllers\PostLikeController@store');

    // download
    Route::get('/attachment/download', 'App\Http\Controllers\PostController@download');

    // comment
    Route::get('/{post_id}/comment', 'App\Http\Controllers\CommentController@index');
    Route::post('/{post_id}/comment', 'App\Http\Controllers\CommentController@store');
    Route::patch('/{post_id}/comment', 'App\Http\Controllers\CommentController@update');
    Route::delete('/{post_id}/comment', 'App\Http\Controllers\CommentController@destroy');

    // comment likes
    Route::post('/{post_id}/comment/{comment_id}/like', 'App\Http\Controllers\CommentLikeController@store');
});

Route::group(['prefix' => 'cart', 'middleware' => 'auth:api'], function ($router) {
    Route::get('/', 'App\Http\Controllers\CartController@index');
    Route::post('/', 'App\Http\Controllers\CartController@store');
    Route::delete('/', 'App\Http\Controllers\CartController@destroy');
    Route::patch('/{id}', 'App\Http\Controllers\CartController@update');
});

Route::group(['prefix' => 'reset'], function ($router) {
    Route::patch('/password', 'App\Http\Controllers\ResetPasswordController@update');
    Route::post('/password/request', 'App\Http\Controllers\ResetPasswordController@store');
    Route::get('/password/{token}', 'App\Http\Controllers\ResetPasswordController@show');
});


Route::group(['prefix' => 'games', 'middleware' => 'auth:api'], function ($router) {
    Route::get('/', 'App\Http\Controllers\GameController@index')->withoutMiddleware('auth:api');
    Route::get('/search', 'App\Http\Controllers\GameController@search');

    // admin
    Route::post('/', 'App\Http\Controllers\GameController@store')->middleware('auth.admin');
    Route::post('/update', 'App\Http\Controllers\GameController@update')->middleware('auth.admin');
    Route::delete('/', 'App\Http\Controllers\GameController@destroy')->middleware('auth.admin');
});

Route::group(['prefix' => 'products', 'middleware' => 'auth:api'], function ($router) {
    Route::get('/', 'App\Http\Controllers\ProductController@index');

    // admin
    Route::post('/', 'App\Http\Controllers\ProductController@store')->middleware('auth.admin');
    Route::post('/update', 'App\Http\Controllers\ProductController@update')->middleware('auth.admin');
    Route::delete('/', 'App\Http\Controllers\ProductController@destroy')->middleware('auth.admin');
    Route::get('/variants/{id}', 'App\Http\Controllers\ProductVariantController@show')->middleware('auth.admin');
});


Route::group(['prefix' => 'orders', 'middleware' => 'auth:api'], function ($router) {
    Route::get('/', 'App\Http\Controllers\OrderController@index');
    Route::post('/', 'App\Http\Controllers\OrderController@store');

    // admin
    Route::get('/admin/search', 'App\Http\Controllers\OrderAdminController@search')->middleware('auth.admin');
    Route::post('/admin', 'App\Http\Controllers\OrderAdminController@store')->middleware('auth.admin');
    Route::patch('/admin', 'App\Http\Controllers\OrderAdminController@update')->middleware('auth.admin');
    Route::delete('/admin', 'App\Http\Controllers\OrderAdminController@destroy')->middleware('auth.admin');
});

Route::group(['prefix' => 'account', 'middleware' => 'auth:api'], function ($router) {
    Route::get('/', 'App\Http\Controllers\AccountController@index');
    Route::post('/update', 'App\Http\Controllers\AccountController@update');
    Route::post('/update/image', 'App\Http\Controllers\AccountController@updateImage');
});

Route::group(['prefix' => 'chat', 'middleware' => 'auth:api'], function ($router) {
    Route::get('/', 'App\Http\Controllers\ChatController@index');
    Route::post('/', 'App\Http\Controllers\ChatController@store');
    // Route::get('/room', 'App\Http\Controllers\ChatRoomController@index');
    Route::get('/room/{id}', 'App\Http\Controllers\ChatRoomController@show');
});

Route::group(['prefix' => 'chat/admin', 'middleware' => 'auth:api'], function ($router) {
    Route::get('/', 'App\Http\Controllers\ChatAdminController@index');
    Route::get('/search', 'App\Http\Controllers\ChatRoomAdminController@search');
    Route::post('/', 'App\Http\Controllers\ChatAdminController@store');
    Route::get('/room', 'App\Http\Controllers\ChatRoomAdminController@index');
    Route::get('/room/show', 'App\Http\Controllers\ChatRoomAdminController@show');
});

Route::group(['prefix' => 'save', 'middleware' => 'auth:api'], function ($router) {
    Route::get('/', 'App\Http\Controllers\SaveController@index');
    Route::post('/', 'App\Http\Controllers\SaveController@store');
    Route::get('download/{id}', 'App\Http\Controllers\SaveController@show');
    Route::delete('/{id}', 'App\Http\Controllers\SaveController@destroy');
});

// });
