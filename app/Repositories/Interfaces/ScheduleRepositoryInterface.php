<?php

namespace App\Repositories\Interfaces;

interface ScheduleRepositoryInterface
{
    public function all(): array;

    public function dummy(): void;
    
    public function live(): void;
    
    public function setDate($dateFrom, $dateTo): void;
}