<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use learntotrade\salesforce\CoachingSession;
use learntotrade\salesforce\fields\CoachingSessionFields;
use learntotrade\salesforce\Person;
use learntotrade\salesforce\fields\PersonFields;
use Carbon\Carbon;
use learntotrade\salesforce\Sale;
use learntotrade\salesforce\fields\SaleFields;
use Illuminate\Support\Arr;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    private $result = [];

    public function all(): array
    {
        if (config('app.enable_api_dummy_data')) {
            $this->dummy();

        } else {
            $this->live();
        }

        return $this->result;
    }

    public function live(): void
    {
        $data = [];

        $where = 'Date__c >= '.date('Y-m-d');

        if (auth()->guard('portal')->check()) {
            $salesforceToken = auth()->guard('portal')->user()->salesforce_token;
            
            $person = resolve(Person::class)->get($salesforceToken);
            
            $sale = resolve(Sale::class)->query(['Id'], [SaleFields::CUSTOMER.' = \''.$salesforceToken.'\'']);
            $saleId = Arr::pluck($sale['records'], 'Id');
            
            $where .= ' and (' . CoachingSessionFields::SALE . ' IN (\'' . implode('\',\'', $saleId) . '\') or ' . 
                      CoachingSessionFields::STATUS . ' = \'Pending\')';
        }

        $sf = resolve(CoachingSession::class)->query(
            array_values(config('api.sf_schedule')),
            [$where]
        );

        if (count($sf)) {
            foreach ($sf['records'] as $field => $value) {
                foreach (config('api.sf_schedule') as $key => $val) {
                    $data[$field][$key] = $value[$val];
                }
            
                $start = Carbon::createFromFormat('Y-m-d h:i', $data[$field]['date'] . ' ' . $data[$field]['start_time'], 'UTC');
                $end = Carbon::createFromFormat('Y-m-d h:i', $data[$field]['date'] . ' ' . $data[$field]['end_time'], 'UTC');

                if (isset($person)) {
                    $start->setTimezone($person[PersonFields::TIMEZONE]);
                    $end->setTimezone($person[PersonFields::TIMEZONE]);
                }

                $start = explode(' ', $start->format('Y-m-d h:i'));
                $end = explode(' ', $end->format('Y-m-d h:i'));
                
                $data[$field]['converted_date'] = $start[0];
                $data[$field]['converted_start_time'] = $start[1];
                $data[$field]['converted_end_time'] = $end[1];
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
}