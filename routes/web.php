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

Route::any('/wechat', 'WeChatController@index');
Route::get('/remark', 'WeChatController@remark');
Route::get('/getAccessToken', 'WeChatController@getAccessToken');
Route::get('/menu', 'WeChatController@createMenu');
Route::get('/tags/users', 'TagController@users');
Route::get('/tags/user/tags', 'TagController@userTags');
Route::resource('/tags', 'TagController');
Route::post('/tags/batchUser', 'TagController@batchUser');
Route::get('/users/batchInfo', 'UsersController@batchInfo');
Route::resource('/users', 'UsersController');
