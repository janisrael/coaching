<?php

namespace App\Http\Controllers\Coaching\V1;

use App\Repositories\Interfaces\CoachRepositoryInterface;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\CoachResource;
use App\Http\Resources\ScheduleResource;

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
}
