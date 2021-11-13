<?php //COACHING VERSION 1

Route::group(['namespace' => 'Coaching\V1'], function () {

    Route::group(['prefix' => 'v1'], function () {
        Route::get('{any?}', 'IndexController@any');
    });

    Route::group(['prefix' => 'api/v1', 'middleware' => 'portal.instance'], function () {

        Route::get('account', 'AccountController@person');

        Route::get('account/sales', 'AccountController@sales');

        Route::get('coaches', 'CoachController@all');

        Route::get('coaches/schedule/{date_from?}/{date_to?}', 'CoachController@schedule');

        Route::post('coaching-session/book', 'CoachingSessionController@book');

        Route::post('coaching-session/cancel', 'CoachingSessionController@cancel');
    });


    Route::group(['prefix' => 'api/v1'], function () {

        Route::get('share', 'CoachController@share');

        Route::get('check-student', 'CoachController@check');
    });


});
