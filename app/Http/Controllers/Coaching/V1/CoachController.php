<?php

namespace App\Http\Controllers\Coaching\V1;

use App\Repositories\Interfaces\CoachRepositoryInterface;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use App\Repositories\Interfaces\SaleRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\CoachResource;
use App\Http\Resources\ScheduleResource;
use App\Http\Resources\SaleResource;

class CoachController extends Controller
{
    private $coachRepository;
    private $scheduleRepository;
    private $saleRepository;

    public function __construct(
        CoachRepositoryInterface $coachRepository, 
        ScheduleRepositoryInterface $scheduleRepository,
        SaleRepositoryInterface $saleRepository
    ){
        $this->coachRepository = $coachRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->saleRepository = $saleRepository;
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
        $data = $this->scheduleRepository->all();

        if (!is_null($date_from) and !is_null($date_to)) {
            $data = $this->scheduleRepository->getByDate($date_from, $date_to);
        }

        return ScheduleResource::collection(collect($data));
    }
    
    /**
     * Get All Coaches Sale
     * 
     * @return json
     */
    public function sale()
    {
        $data = $this->saleRepository->all();

        return SaleResource::collection(collect($data));
    }
}
