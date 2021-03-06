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
Route::post('user/login', ['uses' => 'Api\UserController@userLogin', 'as' => 'user.login.api']);
Route::post('user/create', ['uses' => 'Api\UserController@create', 'as' => 'user.create.api']);
Route::post('user/checkPhone', ['uses' => 'Api\UserController@checkPhone', 'as' => 'user.check.phone.api']);
Route::post('demo/getConversation', ['uses' => 'Api\DemoController@getConversation', 'as' => 'demo.get.conversation']);
Route::group(['middleware' => ['authentication.api']], function () {
    Route::post('user/authentication', ['uses' => 'Api\UserController@authentication', 'as' => 'use.authentication.api']);
    Route::post('contact/list', ['uses' => 'Api\ContactController@getList', 'as' => 'contact.list.api']);
    Route::post('room/list', ['uses' => 'Api\RoomController@getList', 'as' => 'room.list.api']);
});

Route::get('user/userTest', ['uses' => 'Api\UserController@userTest']);
Route::any('test', 'Api\ApiController@test');
