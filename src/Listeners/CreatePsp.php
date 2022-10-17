<?php

namespace Go2Flow\SaasRegisterLogin\Listeners;

use Go2Flow\SaasRegisterLogin\Events\TeamCreated;
use Go2Flow\SaasRegisterLogin\Jobs\CreatePspJob;
use Go2Flow\SaasRegisterLogin\Models\Team;
use Go2Flow\SaasRegisterLogin\Repositories\TeamRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatePsp implements ShouldQueue
{
    private TeamRepositoryInterface $teamRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        TeamRepositoryInterface $teamRepository
    ) {
        $this->teamRepository = $teamRepository;
    }

    /**
     * @param TeamCreated $event
     * @return void
     */
    public function handle(TeamCreated $event)
    {
        CreatePspJob::dispatch($event->team->id)->onQueue(config('saas-register-login.team_creation_queue', 'default'));
    }
}
