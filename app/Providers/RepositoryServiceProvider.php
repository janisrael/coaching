<?php

namespace App\Providers;

use App\Repositories\CoachRepository;
use App\Repositories\Interfaces\CoachRepositoryInterface;
use App\Repositories\ScheduleRepository;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;
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
