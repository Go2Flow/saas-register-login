<?php

namespace Go2Flow\SaasRegisterLogin\Jobs;

use Go2Flow\SaasRegisterLogin\Models\Team;
use Go2Flow\SaasRegisterLogin\Repositories\TeamRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreatePspJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $teamId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $teamId)
    {
        $this->teamId= $teamId;
    }

    public function uniqueId()
    {
        return $this->teamId.'_psp_create';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var Team $team */
        $team = Team::findOrFail($this->teamId);
        /** @var TeamRepositoryInterface $teamRepository */
        $teamRepository = app(TeamRepositoryInterface::class);
        $psp_instance = $teamRepository->createPSPInstanceName($team->name, uniqid());
        if ($team->psp_instance) {
            $psp_instance = $team->psp_instance;
        }
        $psp_id = $teamRepository->createPspMerchant($team, $psp_instance);
        $team->psp_instance = $psp_instance;
        $team->psp_id = $psp_id;
        $team->save();
    }
}
