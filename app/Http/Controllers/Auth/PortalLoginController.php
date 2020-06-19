<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PortalLoginController extends Controller
{
    public function __construct()
    {
        //
    }

    public function login(Request $request)
    {
        Log::info('------------------');
        Log::info('Method: ' . $request->method());
        Log::info('URL: ' . $request->fullUrl());

        return $request->all();
    }
}
