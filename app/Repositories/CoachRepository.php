<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CoachRepositoryInterface;
use Faker\Factory;
use learntotrade\salesforce\User;
use learntotrade\salesforce\fields\UserFields;
use learntotrade\salesforce\fields\PersonFields;

class CoachRepository implements CoachRepositoryInterface
{
    private $result = [];

    private $api_options = [
        'access_group',
        'market_traded',
        'style',
        'languages'
    ];

    private $customer_group = [
        'LTT' => 'Learn To Trade',
        'SC2' => 'Smart Charts',
        'LTT Legacy' => 'LTT Legacy',
    ];

    public function all($resource=''): array
    {
        if (config('app.enable_api_dummy_data')) {
            $this->dummy();

        } else {
            $this->live($resource);
        }

        if (count($this->result['options'])) {
            $options = $this->result['options'];
            foreach ($options as $key => $option) {
                if (in_array($key, $this->api_options)) {
                    $this->result['options'][$key] = array_unique($option);
                }
            }
        }

        return $this->result;
    }

    public function live($resource=''): void
    {
        $data = [];
        $options = [];
        $person = session('sf_customer');
        $customerGroup = $this->customer_group[$person[PersonFields::CUSTOMER_GROUP]] ?? $this->customer_group['SC2'];
        if ($customerGroup == 'LTT Legacy') {
            $customerGroup = $this->customer_group['LTT'];
        }

        $customerType = [
            'Front End' => UserFields::CAN_COACH_FRONT_END.' = true',
            'Back End' => UserFields::CAN_COACH_BACK_END.' = true',
        ];
        
        $filteredCoaches = [
            UserFields::COACH_ASSIGNED_REGION.' includes (\''.$person[PersonFields::REGION].'\')',
            UserFields::BUSINESS_DIVISION.' includes (\''.$customerGroup.'\')',
        ];

        if (! empty($person[PersonFields::CUSTOMER_TYPE])) {
            $filteredCoaches[] = $customerType[$person[PersonFields::CUSTOMER_TYPE]];
        }

        $sf = resolve(User::class)->query(
            array_values(config('api.sf_coaches')), 
            $filteredCoaches
        );

        if (count($sf) > 0 and isset($sf['records'])) {
            foreach ($sf['records'] as $field => $value) {
                foreach (config('api.sf_coaches') as $key => $val) {
                    if (in_array($key, $this->api_options)) {
                        $value[$val] = array_filter(explode(';', $value[$val]));
                        
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

                if (isset($data[$field])) {
                    $data[$field]['country'] = __('country.'.$person[PersonFields::REGION]);
                    $data[$field]['region'] = $person[PersonFields::REGION];
                    $data[$field]['country_code'] = $person[PersonFields::REGION];
                    $data[$field]['assigned_region'] = explode(';',$data[$field]['assigned_region']);
                }
            }
        }

        $this->result = [
            'coaches' => $data,
            'options' => $options,
        ];
    }

    public function getCoachById(string $coachId)
    {
        return resolve(User::class)->get($coachId);
    }

    public function dummy(): void
    {
        $data = [];
        $options = [];

        for ($i=1; $i<=config('app.total_dummy_data'); $i++) {
            $row = [];
            $faker = Factory::create();
            $faker->idNumber = $i;
            
            foreach (config('api.coaches') as $column => $value) {
                $row[$column] = is_array($value) ? $faker->randomElements($value, rand(1,2)) : ($value ? $faker->{$value} : null);
                
                if (is_array($row[$column])) {
                    foreach ($row[$column] as $opt_key => $opt_val) {
                        $options[$column][] = $opt_val;
                    }
                }
            }
            $data[] = $row;
        }

        $this->result = [
            'coaches' => $data,
            'options' => $options,
        ];
    }
}