<?php

namespace App\Repositories\Interfaces;

interface ScheduleRepositoryInterface
{
    public function all();

    public function getByDate(string $date_from, string $date_to);
}