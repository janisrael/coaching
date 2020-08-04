<?php

namespace App\Providers;

use App\Repositories\CoachRepository;
use App\Repositories\Interfaces\CoachRepositoryInterface;
use App\Repositories\ScheduleRepository;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use App\Repositories\SaleRepository;
use App\Repositories\Interfaces\SaleRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            CoachRepositoryInterface::class, 
            CoachRepository::class
        );

        $this->app->bind(
            ScheduleRepositoryInterface::class, 
            ScheduleRepository::class
        );

        $this->app->bind(
            SaleRepositoryInterface::class, 
            SaleRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
