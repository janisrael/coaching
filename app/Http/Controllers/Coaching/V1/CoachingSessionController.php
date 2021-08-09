<?php

namespace App\Http\Controllers\Coaching\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CoachingSessionResource;
use App\Http\Requests\CoachingSessionRequest;
use learntotrade\salesforce\CoachingSession;
use learntotrade\salesforce\fields\CoachingSessionFields;
use App\Repositories\Interfaces\SaleRepositoryInterface;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;

class CoachingSessionController extends Controller
{
    private $saleRepository;
    private $scheduleRepository;

    public function __construct(SaleRepositoryInterface $saleRepository, ScheduleRepositoryInterface $scheduleRepository)
    {
        $this->saleRepository = $saleRepository;
        $this->scheduleRepository = $scheduleRepository;
    }
    
    public function book(CoachingSessionRequest $request)
    {
        $sfSales = $this->saleRepository->all();

        $computedCredits = $this->saleRepository->computedCredits($sfSales['sales']);

        $result = [
            'status' => 'error',
            'message' => 'Booking Error.'
        ];

        if ($computedCredits['total_available'] > 0) {

            $sessionsRemaining = collect($sfSales['sales'])->sortBy('date')->where('sessions_remaining', '>', 0)->first();

            if (resolve(CoachingSession::class)->update($request->schedule_id, [
                CoachingSessionFields::STATUS => 'Booked',
                CoachingSessionFields::SALE => $sessionsRemaining['id'],
            ])) {
                $result = [
                    'status' => 'success',
                    'message' => 'Successfully Booked.'
                ];
            }

        } else {
            $result = [
                'status' => 'error',
                'message' => 'Insufficient Credit.',
            ];
        }

        $result['computed_credits'] = $this->saleRepository->computedCredits($this->saleRepository->all()['sales']);
        $result['schedules'] = $this->scheduleRepository->all()['schedules'];

        return new CoachingSessionResource(collect($result));
    }

    public function cancel(CoachingSessionRequest $request)
    {
        return new CoachingSessionResource(collect(['cancel']));
    }
}
