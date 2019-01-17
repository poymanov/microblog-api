<?php

use Illuminate\Http\Request;

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

Route::group(['prefix' => 'auth', 'as' => 'api.auth.'], function () {
    Route::post('signup', 'Api\AuthController@signup')->name('signup');
    Route::post('login', 'Api\AuthController@login')->name('login');
    Route::get('logout', 'Api\AuthController@logout')->name('logout');
});
