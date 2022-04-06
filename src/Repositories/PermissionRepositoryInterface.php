<?php

namespace Go2Flow\SaasRegisterLogin\Repositories;

use Go2Flow\SaasRegisterLogin\Models\Team;

interface PermissionRepositoryInterface
{
    public function createBasePermissions() :bool;
    public function createBaseRoles(Team $team) :bool;
    public function getAllPermissions() :array;

    /**
     * @param Team|null $team
     * @return array
     */
    public function getRoles(?Team $team = null) :array;
}
