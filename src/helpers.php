<?php

use Go2Flow\SaasRegisterLogin\Models\Team;

if (! function_exists('setSaasTeamId')) {
    /**
     * @param int|string|\Illuminate\Database\Eloquent\Model $id
     *
     */
    function setSaasTeamId($id)
    {
        session()->put('team_id', $id);
    }
}

if (! function_exists('getSaasTeamId')) {
    /**
     * @return int|null
     */
    function getSaasTeamId(): ?int
    {
        return session()->get('team_id', null);
    }
}

if (! function_exists('currentTeam')) {
    /**
     * @return Team|null
     */
    function currentTeam(): ?Team
    {
       return Team::find(getSaasTeamId());
    }

}
