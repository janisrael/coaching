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
        $referer = parse_url($request->headers->get('referer'));
        if ($request->has('pl') === false and isset($referer['query'])) {
            parse_str($referer['query'], $query);
            if (! isset($query['pl'])) {
                abort(response()->json([
                    'error_code' => 403,
                    'error_message' => 'Forbidden',
                ], 200));
            }
            $request->merge(['pl'=>$query['pl']]);
        }
        
        if ($request->has('pl')) {
            $portalUser = PortalLogin::where('api_token', $request->pl)->first();
            if ($portalUser and !$portalUser->can_access_coaching) {
                $portalnstace = explode('\\', session('portal_instance'));
                return response()->json([
                    'error_code' => 403,
                    'error_message' => 'Unable to load ' . ($portalnstace[2] ?? session('portal_instance')) . ' environment.',
                ], 200);
            }
        }

        return $next($request);
    }
}
