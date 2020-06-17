<?php

namespace App\Http\Middleware\Portal;
use Illuminate\Auth\Access\AuthorizationException;

use Closure;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('app.auth_user_portal')) {
            if (! is_null(config('app.portal_url'))) {
                return redirect()->away(config('app.portal_url'));
            }
            
            throw new AuthorizationException();
        }

        return $next($request);
    }
}
