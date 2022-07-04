<?php

namespace Go2Flow\SaasRegisterLogin\Events;

use Illuminate\Queue\SerializesModels;

class TeamCreated
{
    use SerializesModels;
    public $team;
    public function __construct($team)
    {
        $this->team = $team;
    }
}