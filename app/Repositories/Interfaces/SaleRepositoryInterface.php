<?php

namespace App\Repositories\Interfaces;

interface SaleRepositoryInterface
{
    public function all(): array;

    public function dummy(): void;
    
    public function live(): void;
}