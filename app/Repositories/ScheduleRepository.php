<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ScheduleRepositoryInterface;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    public function all(): array
    {
        return [];
    }

    public function getByDate($date_from, $date_to): array
    {
        return [];
    }
}