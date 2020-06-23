<?php

namespace App\Http\Controllers\Coaching\V1;

use App\Repositories\Interfaces\CoachRepositoryInterface;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return 'all';
    }
    /**
     * Get All Coaches Schedule
     * 
     * @return json
     */
    public function schedule($date_from=null, $date_to=null)
    {
        return 'schedule ' . $date_from . ' - ' . $date_to;
    }
}
