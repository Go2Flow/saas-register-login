<?php

namespace Go2Flow\SaasRegisterLogin\Repositories;

use Go2Flow\SaasRegisterLogin\Models\Team;
use Go2Flow\SaasRegisterLogin\Models\Team\Invitation;
use Go2Flow\SaasRegisterLogin\Models\User;
use Spatie\Permission\Models\Role;

class TeamRepository implements TeamRepositoryInterface
{
    /**
     * @param array $data
     * @param User|null $owner
     * @return Team
     */
    public function create(array $data, ?User $owner): Team
    {
        if ($owner) {
            $data['owner_id'] = $owner->id;
            $data['email'] = $data['email'] ?? $owner->email;
        }
        if (is_string($data['languages'])) {
            $data['languages'] = [$data['languages']];
        }
        $data['psp_id'] = uniqid(); // @TODO: hook into psp service to get a real id
        $team = new Team($data);
        $team->save();
        return $team->refresh();
    }

    public function invite(Team $team, string $email, ?int $roleId): bool
    {
        if (!$roleId) {
            $roleId = Role::where('team_id', $team->id)->first()->id;
        }
        $invite = new Invitation();
        $invite->email = $email;
        $invite->role_id = $roleId;
        $invite->team_id = $team->id;
        $invite->save();
        return true;
    }
}
