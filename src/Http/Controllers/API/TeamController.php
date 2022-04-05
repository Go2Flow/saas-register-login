<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers\API;

use Go2Flow\SaasRegisterLogin\Http\Controllers\Controller;
use Go2Flow\SaasRegisterLogin\Models\Team;
use Go2Flow\SaasRegisterLogin\Models\User;

class TeamController extends Controller
{
    function users()
    {
        $team = currentTeam();
        if ($team) {
            return currentTeam()->users;
        }
        abort('401', 'Unauthenticated');
    }
}
