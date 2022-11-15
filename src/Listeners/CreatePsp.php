<?php

namespace Go2Flow\SaasRegisterLogin\Listeners;

use Go2Flow\SaasRegisterLogin\Events\TeamCreated;
use Go2Flow\SaasRegisterLogin\Jobs\CreatePspJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatePsp implements ShouldQueue
{

    /**
     * @param TeamCreated $event
     * @return void
     */
    public function handle(TeamCreated $event)
    {
        if (config('saas-register-login.create_psp', false)) {
            CreatePspJob::dispatch($event->team->id)->onQueue(config('saas-register-login.team_creation_queue', 'default'));
        }
    }
}
