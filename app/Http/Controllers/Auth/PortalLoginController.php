<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PortalLoginRequest;

class PortalLoginController extends Controller
{
    public function __construct()
    {
        //
    }

    public function login(PortalLoginRequest $request)
    {
        return $request->all();
    }
}
