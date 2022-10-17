<?php

namespace Go2Flow\SaasRegisterLogin\Providers;

use Go2Flow\SaasRegisterLogin\Events\TeamCreated;
use Go2Flow\SaasRegisterLogin\Listeners\CreatePsp;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TeamCreated::class => [
            CreatePsp::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}