<?php

namespace App\Providers;

use App\Auth\JwtGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Event' => 'App\Policies\EventPolicy',
        'App\Models\Ticket' => 'App\Policies\TicketPolicy',
        'App\Models\Voucher' => 'App\Policies\VoucherPolicy',
        'App\Models\Booking' => 'App\Policies\BookingPolicy',
        'App\Models\Company' => 'App\Policies\CompanyPolicy'
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::extend('jwt', function ($app, $name, array $config) {
            return new JwtGuard(Auth::createUserProvider($config['provider']), $app['request']);
        });
    }
}
