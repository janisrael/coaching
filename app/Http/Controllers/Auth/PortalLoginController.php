<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortalLoginRequest;
use App\Models\PortalLogin;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PortalLoginController extends Controller
{
    private $portalLogin;

    private $client;

    public function __construct(PortalLogin $portalLogin, Client $client)
    {
        $this->portalLogin = $portalLogin;

        $this->client = $client;
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \App\Http\Requests\PortalLoginRequest  $request
     */
    public function login(PortalLoginRequest $request)
    {
        $url = config('app.portal_session_url').'?token='.$request->token;
        
        $clientRequest = $this->client->get($url); 

        $clientResponse = json_decode($clientRequest->getBody()->getContents());

        $this->portalLogin->updateOrCreate(
            ['portal_user_id' => $clientResponse->user->id, 'salesforce_token' => $clientResponse->user->salesforce_token],
            [
                'portal_user_details' => json_encode($clientResponse->user),
                'api_token' => $clientResponse->token,
                'expired_at' => $clientResponse->expires_at,
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);
    
        $request->session()->regenerate();    
    
        $this->guard()->login($this->portalLogin);

        return redirect()->intended(config('app.coaching_url'));
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();
        
        return redirect()->away(config('app.portal_url'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('portal');
    }
}
