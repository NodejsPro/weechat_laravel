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

Route::post('/user/login', ['uses' => 'UserController@userLoginApi', 'as' => 'user.login.api']);
Route::get('/user/userTest', ['uses' => 'UserController@userTest']);
Route::post('/user/authentication', ['uses' => 'UserController@authenticationApi']);
Route::post('/user/create', ['uses' => 'UserController@createApi', 'as' => 'user.create.api']);
Route::any('test', 'Api\ApiController@test');




Route::any('test', 'Api\ApiController@test');
Route::any('wevnalOnlineEnglishQuestion', 'Api\ApiController@wevnalOnlineEnglishQuestion');
Route::any('wevnalOnlineEnglishQuestionNew', 'Api\ApiController@wevnalOnlineEnglishQuestionNew');

Route::any('checknotexists', 'Api\ApiController@checknotexists');

Route::any('checkexists', 'Api\ApiController@checkexists');

Route::any('random', 'Api\ApiController@random');