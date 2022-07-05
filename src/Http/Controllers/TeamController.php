<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers;

use Go2Flow\SaasRegisterLogin\Models\Team;
use Go2Flow\SaasRegisterLogin\Models\Team\Invitation;
use Go2Flow\SaasRegisterLogin\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function change(Team $team): \Illuminate\Http\RedirectResponse
    {

        setSaasTeamId($team->id);
        setPermissionsTeamId($team->id);

        return redirect()->back();
    }

    /**
     * @param Team $team
     * @param int $invitation
     * @param string $hash
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function inviteAccept(Team $team, int $invitation, string $hash)
    {
        if (auth('web')->user()) {
            auth('web')->logout();
        }
        return redirect($this->localizeUrl('/accept-invite/'.$team->id.'/'.$invitation.'/'.$hash));
    }
}
