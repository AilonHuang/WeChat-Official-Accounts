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
Route::get('/getAccessToken', 'WeChatController@getAccessToken');
Route::get('/menu', 'WeChatController@createMenu');
Route::get('/tags/users', 'TagController@users');
Route::resource('/tags', 'TagController');
Route::post('/tags/batchUser', 'TagController@batchUser');
