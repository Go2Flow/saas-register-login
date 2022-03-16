<?php

namespace Go2Flow\SaasRegisterLogin;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Go2Flow\SaasRegisterLogin\Skeleton\SkeletonClass
 */
class SaasRegisterLoginFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'saas-register-login';
    }
}
