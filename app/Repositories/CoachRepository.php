<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CoachRepositoryInterface;
use Faker\Factory;
use learntotrade\salesforce\User;
use learntotrade\salesforce\fields\UserFields;

class CoachRepository implements CoachRepositoryInterface
{
    private $result = [];

    private $api_options = [
        'access_group',
        'market_traded',
        'style',
        'languages'
    ];

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
            'coaches' => $this->result['data'],
            'options' => $opt
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
            'data' => $data,
            'options' => $options,
        ];
    }

    public function live(): void
    {
        $data = [];
        $options = [];

        $sf = resolve(User::class)->query(
            array_values(config('api.sf_coaches')), 
            [UserFields::BUSINESS_DIVISION.' != \'\'']
        );

        if (count($sf)) {

            $row = [];
            
            foreach ($sf['records'] as $field => $value) {
                foreach (config('api.sf_coaches') as $key => $val) {

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

                    $row[$field][$key] = $value[$val];
                }

                // Set Country Name by Country Code
                $country = null;
                if (isset($value[UserFields::COACH_COUNTRY])) {
                    $country = __('country.'.$value[UserFields::COACH_COUNTRY]);
                }
                $row[$field]['country'] = $country;
            }

            $data = $row;
        }

        $this->result = [
            'data' => $data,
            'options' => $options,
        ];
    }

    public function getResult(): array
    {
        return $this->result;
    }
}