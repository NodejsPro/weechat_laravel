<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::group(['middleware' => ['auth']], function () {

    Route::get('/account/account-information', 'UserController@accountInformation');
    Route::post('/account/account-information', 'UserController@updateAccountInformation');
    Route::get('/account/edit', 'UserController@accountEdit');
    Route::post('/account/update', 'UserController@accountUpdate');
    Route::post('/user/getListUser', ['uses' => 'UserController@getListUser', 'as' => 'user.list']);
    Route::resource('user', 'UserController');
    Route::resource('account', 'UserController@index');
});
Route::get('/', 'Auth\LoginController@showLoginForm');
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('/auth/register', 'Auth\RegisterController@showMailRegisterForm');
Route::post('/auth/register', 'Auth\RegisterController@sendRegisterLink');
Route::get('/auth/confirm/{token}', 'Auth\RegisterController@showRegisterForm');
Route::post('/auth/register/create', 'Auth\RegisterController@register');