<?php

namespace App\Providers;

use App\Services\Agenda\Reschedule\EloquentRescheduleItemSource;
use App\Services\Agenda\Reschedule\RescheduleItemSource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RescheduleItemSource::class, EloquentRescheduleItemSource::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
