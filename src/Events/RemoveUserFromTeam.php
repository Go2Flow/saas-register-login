<?php

namespace Go2Flow\SaasRegisterLogin\Events;

use Illuminate\Queue\SerializesModels;

class RemoveUserFromTeam
{
    use SerializesModels;
    public $team_id;
    public $user_id;
    public $new_user_id;
    public function __construct($user_id, $new_user_id, $team_id)
    {
        $this->user_id = $user_id;
        $this->new_user_id = $new_user_id;
        $this->team_id = $team_id;
    }
}