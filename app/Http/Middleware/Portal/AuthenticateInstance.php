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
            return response()->json([
                'error_code' => 403,
                'error_message' => 'Unable to load ' . session('portal_instance')->name . ' instance environment.',
            ], 200);
        }

        return $next($request);
    }
}
