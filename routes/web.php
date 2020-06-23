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

// Validate Login Portal
Route::get('/', 'Auth\PortalLoginController@login');

// Portal Session
Route::middleware('portal.auth')->group(function () {
    require 'coaching/v1.php';
    # require 'coaching/v2.php';
});


// Console Webtool 
Route::group(['prefix' => 'console'], function () {
    Auth::routes([
        'register' => false,
        'reset' => false,
        'verify' => true,
    ]);

    Route::middleware(['auth', 'verified'])->namespace('Console')->group(function () {
        
        Route::get('/', 'DashboardController@index')->name('dashboard');
        
        //
    });
});
