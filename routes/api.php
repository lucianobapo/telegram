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

Route::post('callback-web-hook/{token}', 'TelegramController@postWebHook');

Route::get('set-web-hook',  'TelegramController@setWebHook');
Route::get('get-web-hook',  'TelegramController@getWebHook');

Route::get('messenger-web-hook',  'TelegramController@messengerWebHook');
Route::post('messenger-web-hook',  'TelegramController@messengerWebHook');
