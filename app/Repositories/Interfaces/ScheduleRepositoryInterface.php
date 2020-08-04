<?php

namespace App\Repositories\Interfaces;

interface ScheduleRepositoryInterface
{
    public function all(): array;

    public function dummy(): void;
    
    public function live(): void;

    public function getResult(): array;

    public function getByDate(string $date_from, string $date_to): array;
}