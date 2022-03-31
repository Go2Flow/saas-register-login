<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers;

use Go2Flow\SaasRegisterLogin\Models\Team;

class TeamController extends Controller
{
    function change(Team $team): \Illuminate\Http\RedirectResponse
    {

        setSaasTeamId($team->id);
        setPermissionsTeamId($team->id);

        return redirect()->back();
    }
}
