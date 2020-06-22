<?php //COACHING VERSION 1

Route::group(['namespace' => 'Coaching\V1', 'prefix' => 'v1'], function () {
    Route::get('{any?}', function () {
        return view('layouts.coaching.v1');
    });

    Route::get('coaches', 'CoachController@all');
    Route::get('coaches/schedule/{date_from?}/{date_to?}', 'CoachController@schedule');
});
