<?php

namespace App\Repositories\Interfaces;

interface ScheduleRepositoryInterface
{
    public function all(): array;

    public function getByDate(string $date_from, string $date_to): array;
}