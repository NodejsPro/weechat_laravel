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
Route::post('room/updateInfo', ['uses' => 'Api\RoomController@updateRoomInfo']);
Route::post('user/create', ['uses' => 'Api\UserController@create', 'as' => 'user.create.api']);
Route::post('user/forgetPassword', ['uses' => 'Api\UserController@forgetPassword', 'as' => 'user.forget.password']);
Route::post('user/checkPhone', ['uses' => 'Api\UserController@checkPhone', 'as' => 'user.check.phone.api']);
Route::post('demo/getConversation', ['uses' => 'Api\DemoController@getConversation', 'as' => 'demo.get.conversation']);
Route::post('user/checkSmsCode', ['uses' => 'Api\UserController@checkSmsCode', 'as' => 'user.check.code.api']);
Route::post('user/updatePassword', ['uses' => 'Api\UserController@updatePassword', 'as' => 'user.update.password.api']);
Route::group(['middleware' => ['authentication.api']], function () {
    Route::post('user/authentication', ['uses' => 'Api\UserController@authentication', 'as' => 'use.authentication.api']);
    Route::post('user/getLog', ['uses' => 'Api\DemoController@getConversation']);
    Route::post('user/userLoginRemember', ['uses' => 'Api\UserController@userLoginRemember']);
    Route::post('contact/list', ['uses' => 'Api\ContactController@getList', 'as' => 'contact.list.api']);
    Route::post('room/list', ['uses' => 'Api\RoomController@getList', 'as' => 'room.list.api']);
    Route::post('room/create', ['uses' => 'Api\RoomController@create', 'as' => 'room.create.api']);
    Route::post('room/update', ['uses' => 'Api\RoomController@update', 'as' => 'room.update.api']);
    Route::post('room/updateUserKey', ['uses' => 'Api\RoomController@updateUserKey']);
});

Route::get('user/userTest', ['uses' => 'Api\UserController@userTest']);
Route::post('user/fileUpload', ['uses' => 'Api\UserController@fileUpload']);
Route::post('user/fileMultiUpload', ['uses' => 'Api\UserController@fileMultiUpload']);
Route::any('test', 'Api\ApiController@test');
