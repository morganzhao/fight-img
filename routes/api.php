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

Route::post('/fight/query','FightController@query')->name('query');

Route::post('/fight/chooseTemplateList','FightController@chooseTemplateList')->name('chooseTemplateList');


Route::post('/fight/syncTemplates','FightController@syncTemplates')->name('syncTemplates');

Route::post('/fight/evilTemplateList','FightController@evilTemplateList')->name('evilTemplateList');

Route::post('/fight/hotSearchList','FightController@hotSearchList')->name('hotSearchList');

Route::post('/fight/hotEmojiList','FightController@hotEmojiList')->name('hotEmojiList');

Route::post('/fight/wxInfo','FightController@wxInfo')->name('wxInfo');

Route::post('/fight/upload','FightController@upload')->name('upload');

Route::post('/fight/save','FightController@save')->name('save');

Route::post('/fight/syncImg','FightController@syncImg')->name('syncImg');

Route::post('/fight/makeGif','FightController@makeGif')->name('makeGif');

Route::post('/fight/makeImgByVideo','FightController@makeImgByVideo')->name('makeImgByVideo');

Route::post('/fight/resourceTemplateInfo','FightController@resourceTemplateInfo')->name('resourceTemplateInfo');
