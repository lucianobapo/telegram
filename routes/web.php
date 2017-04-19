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

Route::get('/', function () {
    return view('welcome');
});

Route::get('get-updates',   'TelegramController@getUpdates');
Route::get('send-message',  'TelegramController@getSendMessage');
Route::post('send-message', 'TelegramController@postSendMessage');
Route::post('send-web-hook/{token}', 'TelegramController@postWebHook');