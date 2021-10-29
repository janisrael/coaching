<?php

namespace App\Repositories\Interfaces;

interface SaleRepositoryInterface
{
    public function all($resource=''): array;

    public function dummy(): void;
    
    public function live($resource=''): void;
}