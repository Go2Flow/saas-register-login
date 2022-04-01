<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function localizeUrl(string $url) {
        if (config('saas-register-login.is_multi_language', false)) {
            $url = '/'.app()->getLocale().$url;
        }
        return $url;
    }
}
