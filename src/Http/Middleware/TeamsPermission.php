<?php

namespace Go2Flow\SaasRegisterLogin\Http\Middleware;

use Go2Flow\SaasRegisterLogin\Models\Team;
use Go2Flow\SaasRegisterLogin\Models\User;

class TeamsPermission {

    public function handle($request, \Closure $next)
    {

        if(!empty(auth()->user()) && auth()->user() instanceof User){
            if (getSaasTeamId() === null) {
                setSaasTeamId(auth()->user()->teams->first()->id);
            }
            // session value set on login
            setPermissionsTeamId(session('team_id'));
        }

        if(!empty(auth()->user()) && auth()->user() instanceof Team){
            if (getSaasTeamId() === null) {
                setSaasTeamId(auth()->user()->id);
            }
            // session value set on login
            setPermissionsTeamId(session('team_id'));
        }
        // other custom ways to get team_id
        /*if(!empty(auth('api')->user())){
            // `getTeamIdFromToken()` example of custom method for getting the set team_id
            setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());
        }*/

        return $next($request);
    }
}
