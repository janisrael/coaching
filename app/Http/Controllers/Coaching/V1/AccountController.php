<?php

namespace App\Http\Controllers\Coaching\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PortalLogin;
use App\Http\Resources\AccountResource;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    private $portalLogin;

    public function __construct(PortalLogin $portalLogin)
    {
        $this->portalLogin = $portalLogin;
    }

    /**
     * Get Person Account
     * 
     * @return json
     */
    public function person()
    {
        return new AccountResource(Auth::guard('portal')->user());
    }
}
