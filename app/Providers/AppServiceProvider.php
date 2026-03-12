<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Appointment;
use App\Policies\AppointmentPolicy;


use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
    protected $policies = [
    Appointment::class => AppointmentPolicy::class,
];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_contains(request()->url(), 'ngrok-free.dev') || str_contains(request()->url(), 'ngrok.io')) {
            URL::forceScheme('https');
        }
    }
}
