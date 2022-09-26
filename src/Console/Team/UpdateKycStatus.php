<?php

namespace Go2Flow\SaasRegisterLogin\Console\Team;

use Carbon\Carbon;
use Go2Flow\SaasRegisterLogin\Models\Team;
use Go2Flow\SaasRegisterLogin\Models\Team\Invitation;
use Go2Flow\SaasRegisterLogin\Repositories\PermissionRepositoryInterface;
use Go2Flow\SaasRegisterLogin\Repositories\TeamRepositoryInterface;
use Go2Flow\SaasRegisterLogin\Repositories\UserRepositoryInterface;
use Illuminate\Console\Command;

class UpdateKycStatus extends Command
{
    protected $signature = 'srl:team_update_kyc_status';
    protected $description = 'Gets all KYC Status for Teams';
    /** @var TeamRepositoryInterface $teamRepository */
    private $teamRepository;
    
    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
        parent::__construct();
    }
    public function handle()
    {
        $this->info('Start srl:team_update_kyc_status');
        $teams = Team::all();
        foreach ($teams as $team) {
            $this->teamRepository->updateKycStatus($team);
        }
        $this->info('Done!');
    }
}
