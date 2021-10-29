<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use learntotrade\salesforce\CoachingSession;
use learntotrade\salesforce\fields\CoachingSessionFields;
use learntotrade\salesforce\Person;
use learntotrade\salesforce\fields\PersonFields;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Repositories\Interfaces\CoachRepositoryInterface;
use App\Repositories\Interfaces\SaleRepositoryInterface;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    private $result = [];

    private $scheduleDate = [];

    public function all($resource=''): array
    {
        if (config('app.enable_api_dummy_data')) {
            $this->dummy();

        } else {
            $this->live($resource);
        }

        return $this->result;
    }

    public function live($resource=''): void
    {
        $data = [];

        $coach = resolve(CoachRepositoryInterface::class)->all();
        $coaches = Arr::pluck($coach['coaches'], 'country_code', 'id');

        $where = [CoachingSessionFields::DATE . ' >= '.date('Y-m-d')];

        if (auth()->guard('portal')->check()) {
            $salesforceToken = auth()->guard('portal')->user()->salesforce_token;
            
            $person = resolve(Person::class)->get($salesforceToken);

            $sale = resolve(SaleRepositoryInterface::class)->all();
            $saleId = Arr::pluck($sale['sales'], 'id');
            
            $where = ['(' . CoachingSessionFields::SALE . ' IN (\'' . implode('\',\'', $saleId) . '\') or ' . 
                            CoachingSessionFields::STATUS . ' = \'Pending\')'];

            if (! empty($this->scheduleDate)) {
                $where[] = CoachingSessionFields::DATE . ' >= '.$this->scheduleDate['from'].' and ' .
                            CoachingSessionFields::DATE . ' <= '.$this->scheduleDate['to'];
            }
        }

        $sf = resolve(CoachingSession::class)->query(
            array_values(config('api.sf_schedule')),
            $where
        );

        if (count($sf)) {
            foreach ($sf['records'] as $field => $value) {
                foreach (config('api.sf_schedule') as $key => $val) {
                    $data[$field][$key] = $value[$val];
                }

                if (isset($coaches[$data[$field]['coach_id']])) {
                    $coachTimezone = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $coaches[$data[$field]['coach_id']])[0];
                    $start = Carbon::createFromFormat('Y-m-d H:i', $data[$field]['date'] . ' ' . $data[$field]['start_time'], $coachTimezone);
                    $end = Carbon::createFromFormat('Y-m-d H:i', $data[$field]['date'] . ' ' . $data[$field]['end_time'], $coachTimezone);

                    if (isset($person)) {
                        $start->setTimezone($person[PersonFields::TIMEZONE]);
                        $end->setTimezone($person[PersonFields::TIMEZONE]);
                    }

                    $start = explode(' ', $start->format('Y-m-d h:i'));
                    $end = explode(' ', $end->format('Y-m-d h:i'));
                    
                    $data[$field]['converted_date'] = $start[0];
                    $data[$field]['converted_start_time'] = $start[1];
                    $data[$field]['converted_end_time'] = $end[1];
                
                } else {
                    unset($data[$field]);
                }
            }
        }

        $this->result = [
            'schedules' => $data,
        ];
    }

    public function dummy(): void
    {
        $this->result = [
            'schedules' => [],
        ];
    }

    public function setDate($dateFrom, $dateTo): void
    {
        $this->scheduleDate = [
            'from' => $dateFrom,
            'to' => $dateTo
        ];
    }
}