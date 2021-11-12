<?php

namespace App\Http\Controllers\Coaching\V1;

use App\Repositories\Interfaces\CoachRepositoryInterface;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\CoachResource;
use App\Http\Resources\ScheduleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Traits\ValidateSessionTrait;

class CoachController extends Controller
{
    use ValidateSessionTrait;

    private $coachRepository;
    private $scheduleRepository;

    public function __construct(CoachRepositoryInterface $coachRepository, ScheduleRepositoryInterface $scheduleRepository)
    {
        $this->coachRepository = $coachRepository;
        $this->scheduleRepository = $scheduleRepository;
    }

    /**
     * Get All Coaches List
     *
     * @return json
     */
    public function all(Request $request)
    {
        $this->validateSession($request);

        return CoachResource::collection(collect($this->coachRepository->all()));
    }

    /**
     * Get All Coaches Schedule
     *
     * @return json
     */
    public function schedule($date_from=null, $date_to=null, Request $request)
    {
        $this->validateSession($request);

        if (!is_null($date_from) and !is_null($date_to)) {
            $this->scheduleRepository->setDate($date_from, $date_to);
        }

        if ($request->has('status')) {
            $this->scheduleRepository->setStatus($request->status);
        }

        $data = $this->scheduleRepository->all();

        return ScheduleResource::collection(collect($data));
    }

    /**
     * share read online live account
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function share(Request $request)
    {
        $apiURL = config('app.share_url').'/v1/coaching/students/' .$request->id. '/opt-data-sharing';

        $headers = [
            'Authorization' => 'Bearer '.config('app.student_token'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache'
        ];

        $client = new Client(['http_errors' => false]); //GuzzleHttp\Client
        $result = $client->post($apiURL, [
            'headers' => $headers
        ]);

        return $result;
    }

    /**
     * check student
     *
     * @return array
     */
    public function check(Request $request)
    {
        $apiURL = config('app.share_url') . '/v1/coaching/students/' .$request->id;

        $headers = [
            'Authorization' => 'Bearer '.config('app.student_token'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache'
        ];

        $client = new Client(['http_errors' => false]); //GuzzleHttp\Client
        $result = $client->get($apiURL, [
            'headers' => $headers
        ]);

        $response = $result->getStatusCode();

        $contents = json_decode($result->getBody(), true);

        $status = false;
        if($response === 200) {
            $status = true;
        }

        $res = [
            'is_student' => $status,
            'data' => $contents
        ];

        return $res;

    }
}
