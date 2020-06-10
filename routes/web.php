<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

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

// Portal Session
Route::middleware('auth:portal')->group(function () {
    
    // DEBUG
    Route::any('/', function () {
        Log::info('------------------');
        Log::info('Method: ' . request()->method());
        Log::info('URL: ' . request()->fullUrl());
        Log::info(request()->all());
        return view('welcome');
    });
});


// Admin Webtool 
Route::group(['prefix' => 'console'], function () {
    Auth::routes([
        'register' => false,
        'reset' => false,
        'verify' => true,
    ]);

    Route::middleware(['auth', 'verified'])->namespace('Admin')->group(function () {
        
        Route::get('/', 'DashboardController@index')->name('home');
        
        //
    });
});
