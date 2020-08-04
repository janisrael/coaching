<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use Faker\Factory;
use learntotrade\salesforce\CoachingSession;
use learntotrade\salesforce\fields\CoachingSessionFields;

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