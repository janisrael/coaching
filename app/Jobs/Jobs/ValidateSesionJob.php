<?php

namespace App\Jobs\Jobs;

use App\Models\PortalLogin;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Throwable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;

class ValidateSesionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $token;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $validateSession = resolve(Client::class)->post(config('app.portal_session_token_url'), [
                'form_params' => [
                    'code' => $this->token
                ],
            ]); 
        } catch (ClientException $e) {
            $portalLogin = PortalLogin::where('api_token', $this->token)->first();
            if (! is_null($portalLogin)) {
                $portalLogin->delete();
            }
        }
    }

    public function failed(Throwable $exception)
    {
        Cache::forget($this->token);
    }
}
