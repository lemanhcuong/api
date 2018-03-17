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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('auth/login', 'UserController@login');
Route::post('auth/register', 'UserController@register');
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::post('user-info', 'UserController@getUserInfo');
    Route::post('update-user-info', 'UserController@updateUserInfo');
    Route::post('logout', 'UserController@logout');
});