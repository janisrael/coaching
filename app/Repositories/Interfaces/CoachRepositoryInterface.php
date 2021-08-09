<?php

namespace App\Repositories\Interfaces;

interface CoachRepositoryInterface
{
    public function all(): array;

    public function dummy(): void;
    
    public function live(): void;
}