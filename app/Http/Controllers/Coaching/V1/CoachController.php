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
                'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5NDlmYWI1Zi1hZmYyLTQ3OGQtODUzOS04MDAwNThkZjZiY2UiLCJqdGkiOiIxNzg3MTNhMDdhNzRhYWUxYTllOTU5Mzc2ZGM3MTc5NDdlMWVhYzZiYjZiMTU1NzliOTllMDA5MmQ1Zjc0NWRiZjhmNzUyZjgwOTg0NzY4MyIsImlhdCI6MTYzNDc1MjYxNi4xNDI0MzYsIm5iZiI6MTYzNDc1MjYxNi4xNDI0NCwiZXhwIjoxNjY2Mjg4NjE2LjEyMDUzMSwic3ViIjoiMzNkYzYyOTMtNWZhNi00MWM5LThhNzctYzE2NzAyYmQ3NWI2Iiwic2NvcGVzIjpbXX0.qU8J7AoXXIDEcT93InnvEMsMyZnfA5sMdRSqC3o5TC4ZKl8i9v-3tNz05BYBS9X4b12ExTAcXGxCk-JSye_bAD1cYnZhxZ-JxQs8UlW5hsTm1Xy4srr5rmPwVLEiRFxczFt0pifS1MeMHM2xrBdIkrvrh7Omt_WyoD8fS7EywZmpc4gpkOOknorGiTk5xZMH-vfsEGov6zGWVWO6OA_Wty2g6HQGO0rDHtdZo83aCfQfQjTFjbV5AAZOurO4S1X04JQQSz3MLWUeNqgz60r3lz7Z4z4Em5xLlOYyCPeFW1q6HL1B1OWBgvaWRCAjImKuZvhel5gGRIpQ6aQOSqAfrKXmWcYGv0Y2W9269SJPjOxdxhg-Ir7sLQBW-DatJjGJD0FRQKxM9H3HMw9iojZGFFZUcqqbB7eP86n3HMvOcSvRxJ_e-kh_uR3YvxamdB1bVePpfWxm8jZO8zYO_Zn9otBVB1h_JiRav___V0R3lqAkJZsLQ6h3zgt1MYHaGdcVmJszrS-oZcpYRGZ_-8l_Zb86BT0febVTjYxhU4_CXiHz8lhJEb2OTfEvbsiFCgAnsIiGRul7yOt0qklZmn1nhclZRz1Xatk9N9w1tWtXt-QOM3mVO5F_m0BHUP_-MHF0MgvJdZr3rRdD5j3oUhU8XtGB4-LBXse6P3BerIpMgv0',
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
        $user_portal_id = $request->id;
        $token = config('api.student_token');
        $apiURL = 'https://dev-api.app/v1/coaching/students/' .$user_portal_id;

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
                'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5NDlmYWI1Zi1hZmYyLTQ3OGQtODUzOS04MDAwNThkZjZiY2UiLCJqdGkiOiIxNzg3MTNhMDdhNzRhYWUxYTllOTU5Mzc2ZGM3MTc5NDdlMWVhYzZiYjZiMTU1NzliOTllMDA5MmQ1Zjc0NWRiZjhmNzUyZjgwOTg0NzY4MyIsImlhdCI6MTYzNDc1MjYxNi4xNDI0MzYsIm5iZiI6MTYzNDc1MjYxNi4xNDI0NCwiZXhwIjoxNjY2Mjg4NjE2LjEyMDUzMSwic3ViIjoiMzNkYzYyOTMtNWZhNi00MWM5LThhNzctYzE2NzAyYmQ3NWI2Iiwic2NvcGVzIjpbXX0.qU8J7AoXXIDEcT93InnvEMsMyZnfA5sMdRSqC3o5TC4ZKl8i9v-3tNz05BYBS9X4b12ExTAcXGxCk-JSye_bAD1cYnZhxZ-JxQs8UlW5hsTm1Xy4srr5rmPwVLEiRFxczFt0pifS1MeMHM2xrBdIkrvrh7Omt_WyoD8fS7EywZmpc4gpkOOknorGiTk5xZMH-vfsEGov6zGWVWO6OA_Wty2g6HQGO0rDHtdZo83aCfQfQjTFjbV5AAZOurO4S1X04JQQSz3MLWUeNqgz60r3lz7Z4z4Em5xLlOYyCPeFW1q6HL1B1OWBgvaWRCAjImKuZvhel5gGRIpQ6aQOSqAfrKXmWcYGv0Y2W9269SJPjOxdxhg-Ir7sLQBW-DatJjGJD0FRQKxM9H3HMw9iojZGFFZUcqqbB7eP86n3HMvOcSvRxJ_e-kh_uR3YvxamdB1bVePpfWxm8jZO8zYO_Zn9otBVB1h_JiRav___V0R3lqAkJZsLQ6h3zgt1MYHaGdcVmJszrS-oZcpYRGZ_-8l_Zb86BT0febVTjYxhU4_CXiHz8lhJEb2OTfEvbsiFCgAnsIiGRul7yOt0qklZmn1nhclZRz1Xatk9N9w1tWtXt-QOM3mVO5F_m0BHUP_-MHF0MgvJdZr3rRdD5j3oUhU8XtGB4-LBXse6P3BerIpMgv0',
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
