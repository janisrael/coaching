<?php //COACHING VERSION 2

Route::group(['namespace' => 'Coaching\V2', 'prefix' => 'v2'], function () {
    Route::get('{any?}', function () {
        return view('layouts.coaching.v1');
    });

    Route::get('coaches', 'CoachController@all');
    Route::get('coaches/schedule/{date_from?}/{date_to?}', 'CoachController@schedule');
});