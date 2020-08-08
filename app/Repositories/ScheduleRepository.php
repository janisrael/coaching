<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use Faker\Factory;
use learntotrade\salesforce\CoachingSession;
use learntotrade\salesforce\fields\CoachingSessionFields;
use learntotrade\salesforce\Person;
use learntotrade\salesforce\fields\PersonFields;
use Carbon\Carbon;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    private $result = [];

    private $api_options = [];

    public function all(): array
    {
        if (config('app.enable_api_dummy_data')) {
            $this->dummy();

        } else {
            $this->live();
        }

        $opt = [];
        if (count($this->result['options'])) {
            foreach ($this->result['options'] as $key=>$option) {
                if (in_array($key, $this->api_options)) {
                    $opt[$key] = array_unique($option);
                }
            }
        }

        return [
            'schedules' => $this->result['data'],
            'options' => $opt
        ];
    }

    public function live(): void
    {
        $data = [];
        $options = [];

        if (auth()->guard('portal')->check()) {
            $person = resolve(Person::class)->get(auth()->guard('portal')->user()->salesforce_token);
        }

        $sf = resolve(CoachingSession::class)->query(
            array_values(config('api.sf_schedule')),
            ['Date__c >= '.date('Y-m-d')]
        );

        if (count($sf)) {
            foreach ($sf['records'] as $field => $value) {
                foreach (config('api.sf_schedule') as $key => $val) {

                    // Extract semicolon to array
                    if (in_array($key, $this->api_options)) {
                        $value[$val] = array_filter(explode(';', $value[$val]));

                        // Collect all given options
                        if (count($value[$val])) {
                            foreach ($value[$val] as $opt_key => $opt_val) {
                                if ($opt_val) {
                                    $options[$key][] = $opt_val;
                                }  
                            }
                        }
                    }
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
            'data' => $data,
            'options' => $options,
        ];
    }

    public function dummy(): void
    {
        $this->result = [
            'data' => [],
            'options' => [],
        ];
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function getByDate($date_from, $date_to): array
    {
        return [];
    }
}