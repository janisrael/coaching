<?php

namespace App\Http\Controllers\Coaching\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CoachingSessionResource;
use App\Http\Requests\CoachingSessionRequest;
use learntotrade\salesforce\CoachingSession;
use learntotrade\salesforce\fields\CoachingSessionFields;
use App\Repositories\Interfaces\SaleRepositoryInterface;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use learntotrade\salesforce\Exceptions\SalesforceException;
use Carbon\Carbon;

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
        $sfSales = $this->saleRepository->all('sales');

        $computedCredits = $this->saleRepository->computedCredits($sfSales['sales']);

        $result = [
            'status' => 'error',
            'message' => 'Unable to book.'
        ];

        if ($computedCredits['total_available'] > 0) {

            $sessionsRemaining = collect($sfSales['sales'])->sortBy('date')
                                                           ->where('sessions_remaining', '>', 0)
                                                           ->first();

            try {
                resolve(CoachingSession::class)->update($request->schedule_id, [
                    CoachingSessionFields::STATUS => 'Booked',
                    CoachingSessionFields::SALE => $sessionsRemaining['id'],
                    CoachingSessionFields::LOCATION => 'Remote',
                ]);
                $result = [
                    'status' => 'success',
                    'message' => 'Successfully Booked.'
                ];
                
            } catch (SalesforceException $e) {
                $result['message'] = parent::catchSalesforceException($e);
            }

        } else {
            $result['message'] = 'Insufficient Credit.';
        }

        return new CoachingSessionResource(
            collect($result)->merge($this->refreshComputedCredits())
        );
    }

    public function cancel(CoachingSessionRequest $request)
    {
        $result = [
            'status' => 'error',
            'coaching_session_id' => null
        ];

        $session = resolve(CoachingSession::class)->get($request->schedule_id);
        
        if (
            empty($session)
            || $session[CoachingSessionFields::STATUS] !== 'Booked'
            || Carbon::createFromFormat(
                'Y-m-d H:i',
                $session[CoachingSessionFields::DATE] . ' ' . $session[CoachingSessionFields::START_TIME]
            )->isPast()
        ) {
            $result['message'] = 'Invalid Schedule ID.';

        } else {

            try {
                resolve(CoachingSession::class)->update($request->schedule_id, [
                    CoachingSessionFields::STATUS => 'Cancelled',
                    CoachingSessionFields::CANCELLED_TO_BE_RECREDITED => 'Yes',
                ]);
                $result = ['status' => 'success'];
                
            } catch (SalesforceException $e) {
                $result['message'] = parent::catchSalesforceException($e);
            }

            // Clone Cancelled CoachSession.
            if ($result['status'] == 'success') {
                try {
                    $coachingSessionId = resolve(CoachingSession::class)->create([
                        CoachingSessionFields::SALE => config('app.no_sale_defined_id'),
                        CoachingSessionFields::STATUS => 'Pending',
                        CoachingSessionFields::DATE => $session[CoachingSessionFields::DATE],
                        CoachingSessionFields::START_TIME => $session[CoachingSessionFields::START_TIME],
                        CoachingSessionFields::END_TIME => $session[CoachingSessionFields::END_TIME],
                        CoachingSessionFields::COACH => $session[CoachingSessionFields::COACH],
                        CoachingSessionFields::AVAILABILITY_TYPE => $session[CoachingSessionFields::AVAILABILITY_TYPE],
                    ]);
                    $result['message'] = 'Successfully Cancelled.';
                    $result['coaching_session_id'] = $coachingSessionId;
                    
                } catch (SalesforceException $e) {
                    $result = [
                        'status' => 'error',
                        'message' => parent::catchSalesforceException($e),
                    ];
                }
            }
        }

        return new CoachingSessionResource(
                    collect($result)->merge($this->refreshComputedCredits())
                );
    }

    private function refreshComputedCredits()
    {
        return [
            'computed_credits' => $this->saleRepository->computedCredits($this->saleRepository->all()['sales']),
            //'schedules' => $this->scheduleRepository->all()['schedules'],
        ];
    }
}
