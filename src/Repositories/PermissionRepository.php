<?php

namespace Go2Flow\SaasRegisterLogin\Repositories;

use Go2Flow\SaasRegisterLogin\Models\Team;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class PermissionRepository implements PermissionRepositoryInterface {

    public array $basePermissions = [
        'users.index',
        'users.create',
        'users.edit',
        'users.delete',
        'users.assign.role',

        'roles.index',
        'roles.create',
        'roles.edit',
        'roles.delete',

        'teams.create',
        'teams.edit',
        'teams.subscription',
        'teams.invoices',
    ];

    public array $systemPermissions = [];

    public array $defaultRoles = [
        'Administrator' => 'all',
        'Editor' => [
            'users.index',
            'users.create',
            'users.edit',
            'teams.edit',
            'teams.invoices',
        ]
    ];

    public function createBasePermissions() :bool
    {
        foreach ($this->getAllPermissions() as $permission) {
            Permission::findOrCreate($permission);
        }

        return true;
    }

    public function createBaseRoles(Team $team) :bool
    {
        // Save the current Team ID
        $savePermTeamId = getPermissionsTeamId();

        foreach ($this->defaultRoles as $defaultRole => $permissions) {
            setPermissionsTeamId($team->id);
            $defaultRole = Role::findOrCreate($defaultRole);

            if($permissions == 'all') {
                $permissions = Permission::all();
            }

            $defaultRole->syncPermissions($permissions);
        }

        // Reset to Team ID before using the function
        setPermissionsTeamId($savePermTeamId);

        return true;
    }

    public function getAllPermissions() :array
    {
        return array_merge($this->basePermissions, $this->systemPermissions);
    }

    /**
     * @param Team|null $team
     * @return array
     */
    public function getRoles(?Team $team = null):array
    {
        if (!$team) {
            if (!$team = Team::find(getPermissionsTeamId())) {
                return [];
            }
        }
        return Role::where('team_id', $team->id)->get()->toArray();
    }

}
