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
use App\Traits\ValidateSessionTrait;
use Illuminate\Support\Str;

class CoachingSessionController extends Controller
{
    use ValidateSessionTrait;
    
    private $saleRepository;
    private $scheduleRepository;

    public function __construct(SaleRepositoryInterface $saleRepository, ScheduleRepositoryInterface $scheduleRepository)
    {
        $this->saleRepository = $saleRepository;
        $this->scheduleRepository = $scheduleRepository;
    }
    
    public function book(CoachingSessionRequest $request)
    {
        $this->validateSession($request);
        $this->setLog('BOOK: REQUEST', $request->all());

        $sfSales = $this->saleRepository->all('sales')['sales'];

        $computedCredits = $this->saleRepository->computedCredits($sfSales);
        $this->setLog('BOOK: COMPUTED_CREDIT', $computedCredits);

        $result = [
            'status' => 'error',
            'message' => 'Unable to book.'
        ];

        if ($computedCredits['total_available'] > 0) {
            $sales = collect($sfSales);

            $sessionsRemaining = $sales->where('sessions_expiry', '>', now()->format('Y-m-d'))
                                        ->where('sessions_remaining', '>', 0);

            $getSale = $sessionsRemaining->where('payment_schedule', '=', 'Fully Paid');

            if ($getSale->count() == 0) {
                $getSale = $sessionsRemaining->where('payment_schedule', '=', null)
                                             ->where('is_child_sale', '=', true);
            }

            $sale = $getSale->sortBy('sessions_expiry')->first();
            $this->setLog('BOOK: SALE_SESSION_REMAINING', $sale);

            try {
                $coachingSessionData = [
                    CoachingSessionFields::STATUS => 'Booked',
                    CoachingSessionFields::SALE => $sale['id'],
                    CoachingSessionFields::LOCATION => 'Remote',
                ];
                $this->setLog('BOOK: PREPARE_UPDATE', ['schedule_id' => $request->schedule_id, 'data'=>$coachingSessionData]);
                
                resolve(CoachingSession::class)->update($request->schedule_id, $coachingSessionData);
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

        $this->setLog('BOOK: RESULT', $result);
        return new CoachingSessionResource(
            collect($result)->merge($this->refreshComputedCredits())
        );
    }

    public function cancel(CoachingSessionRequest $request)
    {
        $this->validateSession($request);
        $this->setLog('CANCEL: REQUEST', $request->all());

        $result = [
            'status' => 'error',
            'coaching_session_id' => null
        ];

        $session = resolve(CoachingSession::class)->get($request->schedule_id);
        $this->setLog('CANCEL: COACH_SESSION_DETAILS', $session);

        $cancelFailed = $this->validateCancelBooking($session, $request);
        if (! empty($cancelFailed)) {
            $this->setLog('CANCEL: FAILED', $cancelFailed);
            $result['message'] = 'Invalid Schedule ID.';

        } else {

            $coachingSessionData = [
                CoachingSessionFields::STATUS => 'Cancelled',
                CoachingSessionFields::CANCELLED_TO_BE_RECREDITED => 'Yes',
            ];
            $this->setLog('CANCEL: PREPARE_UPDATE', ['schedule_id'=>$request->schedule_id, 'data'=>$coachingSessionData]);

            try {
                resolve(CoachingSession::class)->update($request->schedule_id, $coachingSessionData);
                $result = ['status' => 'success'];
                
            } catch (SalesforceException $e) {
                $result['message'] = parent::catchSalesforceException($e);
            }

            // Clone Cancelled CoachSession.
            if ($result['status'] == 'success') {
                $cloneCoachingSession = [
                    CoachingSessionFields::SALE => config('app.no_sale_defined_id'),
                    CoachingSessionFields::STATUS => 'Pending',
                    CoachingSessionFields::DATE => $session[CoachingSessionFields::DATE],
                    CoachingSessionFields::START_TIME => $session[CoachingSessionFields::START_TIME],
                    CoachingSessionFields::END_TIME => $session[CoachingSessionFields::END_TIME],
                    CoachingSessionFields::COACH => $session[CoachingSessionFields::COACH],
                    CoachingSessionFields::AVAILABILITY_TYPE => $session[CoachingSessionFields::AVAILABILITY_TYPE],
                    CoachingSessionFields::PREVIOUSLY_CANCELLED => true,
                ];
                $this->setLog('CANCEL: CLONE_COACHING_SESSON', $cloneCoachingSession);

                try {
                    $coachingSessionId = resolve(CoachingSession::class)->create($cloneCoachingSession);
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
        
        $this->setLog('CANCEL: RESULT', $result);
        return new CoachingSessionResource(
                    collect($result)->merge($this->refreshComputedCredits())
                );
    }

    private function refreshComputedCredits()
    {
        return [
            'computed_credits' => $this->saleRepository->computedCredits($this->saleRepository->all()['sales']),
        ];
    }

    private function validateCancelBooking($session, $request)
    {
        $failed = [];

        if (empty($session)) {
            $failed[] = ['no_record_found' => $request->schedule_id];      

        } else {
            if ($session[CoachingSessionFields::STATUS] !== 'Booked') {
                $failed[] = ['status_not_booked' => $session[CoachingSessionFields::STATUS]];            
            }

            $coachingSessionDate = $session[CoachingSessionFields::DATE] . ' ' . $session[CoachingSessionFields::START_TIME];
            if (Carbon::createFromFormat(
                'Y-m-d H:i',
                $coachingSessionDate
            )->isPast()) {
                $failed[] = ['session_date_passed' => $coachingSessionDate];            
            }            
        }
        
        return $failed;
    }
}
