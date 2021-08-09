<?php

namespace App\Http\Middleware\Portal;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Closure;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard=null)
    {
        if (Auth::guard($guard)->check() and $guard == 'portal') {
            return $next($request);
        }

        if (! config('app.auth_user_portal')) {
            return $next($request);
        }

        if (! is_null(config('app.portal_url'))) {
            return redirect()->away(config('app.portal_url'));
        }

        throw new AuthorizationException();
    }
}
