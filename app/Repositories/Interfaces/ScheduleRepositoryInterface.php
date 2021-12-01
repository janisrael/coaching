<?php

namespace App\Repositories\Interfaces;

interface ScheduleRepositoryInterface
{
    public function all($resource=''): array;

    public function dummy(): void;
    
    public function live($resource=''): void;
    
    public function setDate($dateFrom, $dateTo): void;
}