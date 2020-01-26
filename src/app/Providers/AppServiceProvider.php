<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Observers\EventObserver;
use App\Observers\TicketObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Event::observe(EventObserver::class);
        Ticket::observe(TicketObserver::class);
    }
}
