<?php

namespace App\Providers;

use App\Mail\SentMailLogger;
use App\Services\Agenda\Reschedule\EloquentRescheduleItemSource;
use App\Services\Agenda\Reschedule\RescheduleItemSource;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
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
        // Trace de chaque mail envoyé (canal de log « mails »)
        Event::listen(MessageSent::class, SentMailLogger::class);
    }
}
