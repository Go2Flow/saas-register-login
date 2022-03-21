<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    function check($permissionName): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        if (! Auth::user()->hasPermissionTo($permissionName)) {
            abort(403);
        }
        return response('', 204);
    }
}
