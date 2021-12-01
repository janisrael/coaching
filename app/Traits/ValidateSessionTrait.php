<?php

namespace App\Traits;

use App\Jobs\Jobs\ValidateSesionJob;
use App\Models\PortalLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use learntotrade\salesforce\Person;
use learntotrade\salesforce\Exceptions\SalesforceException;

trait ValidateSessionTrait
{
    public function validateSession(Request $request): void
    {
        if ($request->has('pl')) {
            $user = $this->getUser($request->pl);

            if (is_null($user)) {
                session()->forget('portal_user');
                abort(response()->json([
                    'error_code' => 401,
                    'error_message' => 'Unauthorized',
                ], 200));

            } else {
                if (! Cache::has($request->pl)) {
                    Cache::put($request->pl, time(), 120);
                    ValidateSesionJob::dispatch($request->pl);
                }

                try {
                    $sfCustomer = resolve(Person::class)->get($user->salesforce_token);
                    
                } catch (SalesforceException $e) {
                    $sfCustomer = [];
                }

                session([
                    'portal_user' => $user,
                    'sf_customer' => $sfCustomer
                ]);
            }
        }
    }

    public function getUser($token)
    {
       return PortalLogin::where('api_token', $token)->first();
    }
}