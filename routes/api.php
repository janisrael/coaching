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
Route::middleware('portal.auth')->group(function () {
    Route::group(['namespace' => 'Coaching\V1', 'prefix' => 'v1/coaches'], function () {
        Route::get('/', 'CoachController@all');
        Route::get('schedule/{date_from?}/{date_to?}', 'CoachController@schedule');
    });
});
