<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CoachRepositoryInterface;
use Faker\Factory;

class CoachRepository implements CoachRepositoryInterface
{
    public function all(): array
    {
        $api_coaches = config('api.coaches');

        $api_options = [
            'access_group',
            'market_traded',
            'style',
            'languages'
        ];

        $data = [];
        $options = [];

        if (config('app.enable_api_dummy_data')) {
            for ($i=1; $i<=config('app.total_dummy_data'); $i++) {
                $row = [];
                $faker = Factory::create();
                $faker->idNumber = $i;
                
                foreach ($api_coaches as $column => $value) {
                    $row[$column] = is_array($value) ? $faker->randomElements($value, rand(1,2)) : $faker->{$value};
                    
                    if (is_array($row[$column])) {
                        foreach ($row[$column] as $opt_key => $opt_val) {
                            $options[$column][] = $opt_val;
                        }
                    }
                }

                $data['coaches'][] = $row;
            }

            $opt = [];
            foreach ($options as $key=>$option) {
                if (in_array($key, $api_options)) {
                    $opt[$key] = array_unique($option);
                }
            }

            $data['options'] = $opt;
        }

        return $data;
    }
}