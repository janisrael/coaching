<?php

namespace App\Http\Controllers\Coaching\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function any(Request $request)
    {
        $appToken = $request->has('pl') ? $request->pl : '';

        return view('layouts.coaching.v1', compact('appToken'));
    }
}
