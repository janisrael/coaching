<?php //COACHING VERSION 2

Route::group(['namespace' => 'Coaching\V2', 'prefix' => 'v2'], function () {
    Route::get('{any?}', function () {
        return view('layouts.coaching.v2');
    });
});