<?php

namespace App\Providers;

use App\Events\LeadCreated;
use App\Events\SaleCreated;
use App\Listeners\MarkAttendanceOnLogin;
use App\Listeners\LogUserLogout;
use App\Listeners\SendLeadCreatedNotification;
use App\Listeners\SendSaleCreatedNotification;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            MarkAttendanceOnLogin::class,
        ],
        Logout::class => [
            LogUserLogout::class,
        ],
        LeadCreated::class => [
            SendLeadCreatedNotification::class,
        ],
        SaleCreated::class => [
            SendSaleCreatedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

