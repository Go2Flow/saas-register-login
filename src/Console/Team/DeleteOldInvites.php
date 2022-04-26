<?php

namespace Go2Flow\SaasRegisterLogin\Console\Team;

use Carbon\Carbon;
use Go2Flow\SaasRegisterLogin\Models\Team\Invitation;
use Illuminate\Console\Command;

class DeleteOldInvites extends Command
{
    protected $signature = 'srl:team_clean_invites';
    protected $description = 'Deletes old Team Invites';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $this->info('Start srl:team_clean_invites');
        Invitation::query()->where('created_at', '<', Carbon::now()->subDays(7))->delete();
        $this->info('Done!');
    }
}
