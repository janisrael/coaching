<?php

namespace App\Traits;

use App\Jobs\Jobs\ValidateSesionJob;
use App\Models\PortalLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

trait ValidateSessionTrait
{
    public function validateSession(Request $request): void
    {
        if ($request->has('pl')) {
            $user = $this->getUser($request->pl);

            if (is_null($user)) {
                session()->forget('portal_user');
                abort(401);

            } else {
                if (! Cache::has($request->pl)) {
                    Cache::put($request->pl, time(), 120);
                    ValidateSesionJob::dispatch($request->pl);
                }
                
                session(['portal_user' => $user]);
            }
        }
    }

    public function getUser($token)
    {
       return PortalLogin::where('api_token', $token)->first();
    }
}