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

Route::post('/fight/index','FightController@index')->name('index');

Route::post('/fight/chooseTemplateList','FightController@chooseTemplateList')->name('chooseTemplateList');


Route::post('/fight/syncTemplates','FightController@syncTemplates')->name('syncTemplates');

Route::post('/fight/evilTemplateList','FightController@evilTemplateList')->name('evilTemplateList');

Route::post('/fight/hotSearchList','FightController@hotSearchList')->name('hotSearchList');
