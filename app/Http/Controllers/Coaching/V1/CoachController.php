<?php

namespace App\Http\Controllers\Coaching\V1;

use App\Repositories\Interfaces\CoachRepositoryInterface;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\CoachResource;
use App\Http\Resources\ScheduleResource;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CoachController extends Controller
{
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
    public function all()
    {
        return CoachResource::collection(collect($this->coachRepository->all()));
    }

    /**
     * Get All Coaches Schedule
     *
     * @return json
     */
    public function schedule($date_from=null, $date_to=null)
    {
        if (!is_null($date_from) and !is_null($date_to)) {
            $this->scheduleRepository->setDate($date_from, $date_to);
        }

        $data = $this->scheduleRepository->all();

        return ScheduleResource::collection(collect($data));
    }

    /**
     * share read online live account
     *
     * @return JsonResponse
     */
    public function share(Request $request)
    {
        $user_portal_id = $request->id;
        $token = config('api.student_token');
        $apiURL = 'https://dev-api.smartchartsfx.com/v1/coaching/students/' .$user_portal_id. '/opt-data-sharing';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' .$token,
                'Accept: application/json',
                'Content-Type: application/json',
                'Cache-Control: no-cache'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}
