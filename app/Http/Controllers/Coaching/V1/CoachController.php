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
        $apiURL = config('app.share_url').'/v1/coaching/students/' .$request->id. '/opt-data-sharing';
        $curl = curl_init($apiURL);

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.config('app.student_token'),
                'Accept: application/json',
                'Content-Type: application/json',
                'Cache-Control: no-cache'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    /**
     * check student
     *
     * @return JsonResponse
     */
    public function check(Request $request)
    {
        $apiURL = config('app.check_url') . '/v1/coaching/students/' . $request->id;
        $curl = curl_init($apiURL);

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_NOBODY => false,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.config('app.student_token'),
                'Accept: application/json',
                'Content-Type: application/json',
                'Cache-Control: no-cache'
            ),
        ));

        $output = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $status = false;
        if($httpcode != 0) {
            $status = true;
        }
        $response = [
            'is_student' => $status,
            'data' => $output
        ];

        return $response;
    }
}
