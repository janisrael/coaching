<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CoachRepositoryInterface;
use Faker\Factory;

class CoachRepository implements CoachRepositoryInterface
{
    public function all(): array
    {
        $data = [];

        if (config('app.enable_api_dummy_data')) {
            for ($i=1; $i<=config('app.total_dummy_data'); $i++) {
                $row = [];
                $faker = Factory::create();
                $faker->idNumber = $i;
                
                foreach (config('api.coaches') as $column => $value) {
                    $row[$column] = is_array($value) ? $faker->randomElements($value, rand(1,2)) : $faker->{$value};
                }
                $data[] = $row;
            }
        }

        return $data;
    }
}