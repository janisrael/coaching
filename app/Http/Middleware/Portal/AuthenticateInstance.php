<?php

namespace App\Http\Middleware\Portal;
use Closure;

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
        if ($request->session()->has('portal_instance')) {
            $portalnstace = explode('\\', session('portal_instance'));
            return response()->json([
                'error_code' => 403,
                'error_message' => 'Unable to load ' . ($portalnstace[2] ?? session('portal_instance')) . ' environment.',
            ], 200);
        }

        return $next($request);
    }
}
