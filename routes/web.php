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

// Login Portal
Route::get('/', 'Auth\PortalLoginController@loginPortal');
Route::get('logout', 'Auth\PortalLoginController@logout');

// Session Token
Route::get('session/token/{token}', 'Auth\PortalLoginController@sessionToken');

// Portal Session
Route::group(['middleware' => 'portal.auth:portal'], function () {
    
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
