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


// Route::get('/test', function () {
//     return response()->json(['message' => 'test'], 200);
// })->middleware('cors');


Route::group(['prefix' => 'test'], function ($router) {
    Route::get('/', 'App\Http\Controllers\TestController@index');
    Route::post('/', 'App\Http\Controllers\TestController@store');
});

Route::group(['prefix' => 'auth'], function ($router) {
    Route::get('/{provider}/redirect', 'App\Http\Controllers\SocialiteController@redirect');
    Route::get('/{provider}/callback', 'App\Http\Controllers\SocialiteController@callback');
});


Route::get('/', function () {
    return view('welcome');
});
