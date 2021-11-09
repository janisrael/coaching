<?php

namespace App\Http\Controllers\Coaching\V1;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function any()
    {
        return view('layouts.coaching.v1');
    }
}
