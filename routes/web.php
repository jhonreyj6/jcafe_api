<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'auth'], function ($router) {
    Route::get('/{provider}/redirect', 'App\Http\Controllers\SocialiteController@redirect')->name('redirect.provider');
    Route::get('/{provider}/callback', 'App\Http\Controllers\SocialiteController@callback')->name('callback.provider');
});


Route::get('/', function () {
    return view('welcome');
});
