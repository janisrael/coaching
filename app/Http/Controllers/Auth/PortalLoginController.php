<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Requests\PortalLoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\PortalLogin;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;

class PortalLoginController extends Controller
{
    use AuthenticatesUsers;

    private $portalLogin;

    private $client;

    public function __construct(PortalLogin $portalLogin, Client $client)
    {
        $this->middleware('guest')->except('logout');

        $this->portalLogin = $portalLogin;

        $this->client = $client;
    }

    public function sessionToken(Request $request, $token)
    {
        try {
            $clientRequest = $this->client->post(config('app.portal_session_url'), [
                'form_params' => [
                    'code' => $token
                ],
            ]); 

        } catch (ClientException $e) {
            $response = $e->getResponse();
            return [
                'error_code' => $response->getStatusCode(),
                'error_message' => $response->getBody()->getContents(),
            ];
        }

        return $this->processLogin($request, $clientRequest);
    }

    public function loginPortal(PortalLoginRequest $request)
    {
        try {
            $clientRequest = $this->client->get(config('app.portal_session_url').'?token='.$request->token);

        } catch (ClientException $e) {
            $response = $e->getResponse();
            return [
                'error_code' => $response->getStatusCode(),
                'error_message' => $response->getBody()->getContents(),
            ];
        }

        return $this->processLogin($request, $clientRequest);
    }

    private function processLogin($request, $clientRequest)
    {
        $clientResponse = json_decode($clientRequest->getBody()->getContents());

        $password = $clientResponse->user->id . $clientResponse->token . config('app.coaching_url');

        $clientResponse->user->post_login_url = optional($clientResponse)->post_login_url;
        $portalSession = $this->portalLogin->updateOrCreate(
            ['portal_user_id' => $clientResponse->user->id, 'salesforce_token' => $clientResponse->user->salesforce_token],
            [
                'portal_user_details' => json_encode($clientResponse->user),
                'api_token' => $clientResponse->token,
                'email' => $clientResponse->user->email,
                'password' => $password,
                'expired_at' => $clientResponse->expires_at,
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
                'can_access_coaching' => $clientResponse->user->accessControlList->coaching,
            ]);

        $request->merge([
                'email' => $portalSession->email, 
                'password' => $password
                ]);

        return redirect('/v1/?pl='.$clientResponse->token);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();
        
        return redirect()->away(config('app.portal_url'));
    }

    protected function redirectPath()
    {
        return config('app.coaching_url');
    }

    protected function guard()
    {
        return Auth::guard('portal');
    }
}
