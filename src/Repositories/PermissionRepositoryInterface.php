<?php

namespace Go2Flow\SaasRegisterLogin\Repositories;

use Go2Flow\SaasRegisterLogin\Models\Team;

interface PermissionRepositoryInterface
{
    const ROLE_ADMIN_NAME = 'Administrator';
    const ROLE_EDITOR_NAME = 'Editor';
    const ROLE_INSTRUCTOR_NAME = 'Instructor';

    public function createBasePermissions() :bool;
    public function createBaseRoles(Team $team) :bool;
    public function getAllPermissions() :array;

    /**
     * @param Team|null $team
     * @return array
     */
    public function getRoles(?Team $team = null) :array;
}
