<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CoachRepositoryInterface;
use Faker\Factory;
use learntotrade\salesforce\User;
use learntotrade\salesforce\Person;
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

    public function all(): array
    {
        if (config('app.enable_api_dummy_data')) {
            $this->dummy();

        } else {
            $this->live();
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

    public function live(): void
    {
        $data = [];
        $options = [];
        $person = resolve(Person::class)->get(auth()->guard('portal')->user()->salesforce_token);
        $customerGroup = $this->customer_group[$person[PersonFields::CUSTOMER_GROUP]] ?? $this->customer_group['SC2'];
        
        $sf = resolve(User::class)->query(
            array_values(config('api.sf_coaches')), 
            [
                UserFields::REGION.' = \''.$person[PersonFields::REGION].'\'',
                UserFields::BUSINESS_DIVISION.' != \'\'',
                //UserFields::CUSTOMER_TYPE.' = \''.$person[PersonFields::CUSTOMER_TYPE].'\'',
            ]
        );

        if (count($sf)) {
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

                if (isset($data[$field]['access_group']) and 
                    !in_array($customerGroup, $data[$field]['access_group'])) {
                    unset($data[$field]);
                }

                if (isset($data[$field])) {
                    $country = null;
                    if (isset($value[UserFields::COACH_COUNTRY])) {
                        $country = __('country.'.$value[UserFields::COACH_COUNTRY]);
                    }
                    $data[$field]['country'] = $country;
                }
            }
        }

        $this->result = [
            'coaches' => $data,
            'options' => $options,
        ];
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