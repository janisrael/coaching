<?php

namespace App\Http\Middleware\Portal;

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
        // redirect()->away(config('app.portal_url'));
        return $next($request);
    }
}
