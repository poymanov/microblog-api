<?php

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
    Route::post('signup', 'AuthController@signup')->name('signup');
    Route::post('login', 'AuthController@login')->name('login');
    Route::get('logout', 'AuthController@logout')->name('logout');
});

Route::group(['prefix' => 'posts', 'as' => 'api.posts.'], function () {
    Route::get('{user}', 'PostsController@index')->name('user');
    Route::post('', 'PostsController@store')->name('store');
    Route::delete('{post}', 'PostsController@destroy')->name('destroy');
});

Route::group(['prefix' => 'users', 'as' => 'api.users.'], function () {
    Route::get('{id}', 'UsersController@show')->name('show');
    Route::patch('', 'UsersController@update')->name('update');
    Route::post('{id}/subscribe', 'SubscriptionsController@subscribe')->name('subscribe');
    Route::delete('{id}/unsubscribe', 'SubscriptionsController@unsubscribe')->name('unsubscribe');
});
