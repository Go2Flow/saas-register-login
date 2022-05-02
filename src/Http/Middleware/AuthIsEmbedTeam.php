<?php

namespace Go2Flow\SaasRegisterLogin\Http\Middleware;

use Go2Flow\SaasRegisterLogin\Models\Team;

class AuthIsEmbedTeam {

    public function handle($request, \Closure $next)
    {

        if(! auth()->user() instanceof Team){
            abort(response()->json(
                [
                    'api_status' => '401',
                    'message' => 'Unauthenticated',
                ], 401));
        }


        return $next($request);
    }
}
