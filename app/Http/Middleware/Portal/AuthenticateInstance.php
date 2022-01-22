<?php

namespace App\Http\Middleware\Portal;
use Closure;
use App\Models\PortalLogin;

class AuthenticateInstance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $exclude = ['api/v1/account/sales'];
        
        if (! in_array($request->route()->uri, $exclude)) {

            $referer = parse_url($request->headers->get('referer'));

            if ($request->has('pl') === false and isset($referer['query'])) {
                parse_str($referer['query'], $query);
                $request->merge(['pl'=>$query['pl']]);
            }
            
            if ($request->has('pl')) {
                $portalUser = PortalLogin::where('api_token', $request->pl)->first();
                
                if ($portalUser) {
                    if (! $portalUser->can_access_coaching) {
                        $portalUserDetails = json_decode($portalUser->portal_user_details);
                        $portalInstance = explode('\\', $portalUserDetails->instance);
                        return response()->json([
                            'error_code' => 403,
                            'error_message' => 'Unable to load ' . ($portalInstance[2] ?? $portalUserDetails->instance) . ' environment.',
                        ], 200);
                    }

                } else {
                    abort(response()->json([
                        'error_code' => 401,
                        'error_message' => 'Unauthorized',
                    ], 200));
                }

            } else {
                abort(response()->json([
                    'error_code' => 400,
                    'error_message' => 'Bad Request',
                ], 200));
            }
        }

        return $next($request);
    }
}
