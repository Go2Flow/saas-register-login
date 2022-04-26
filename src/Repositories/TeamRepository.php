<?php

namespace Go2Flow\SaasRegisterLogin\Repositories;

use Go2Flow\SaasRegisterLogin\Mail\Invitation as InvitationMail;
use Go2Flow\SaasRegisterLogin\Models\Team;
use Go2Flow\SaasRegisterLogin\Models\Team\Invitation;
use Go2Flow\SaasRegisterLogin\Models\User;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
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
        $team->refresh();
        app(PermissionRepositoryInterface::class)->createBaseRoles($team);
        if ($owner) {
            $role = Role::query()
                ->where('name', PermissionRepositoryInterface::ROLE_ADMIN_NAME)
                ->where('team_id', $team->id)
                ->first();
            if ($role) {
                setPermissionsTeamId($team->id);
                $owner->assignRole($role);
            }
        }
        return $team;
    }

    public function invite(Team $team, string $email, ?int $roleId): bool
    {
        $alreadyInTeam = $team->users()->where('email', $email)->count();
        if ($alreadyInTeam || Invitation::where('email',$email)->where('team_id', $team->id)->first()) {
            return false;
        }
        if (!$roleId) {
            $roleId = Role::where('team_id', $team->id)->first()->id;
        }
        $invite = new Invitation();
        $invite->email = $email;
        $invite->role_id = $roleId;
        $invite->team_id = $team->id;
        $invite->save();

        Mail::to($email)->send(new InvitationMail($invite, app()->getLocale()));
        return true;
    }

    /**
     * @param Team $team
     * @param array $data
     * @return Team
     */
    public function update(Team $team, array $data): Team
    {
        if (
            isset($data['owner_id'])
            && $team->owner_id != $data['owner_id']
        ) {
            if (auth()->user()->id !== $team->owner_id) {
                unset($data['owner_id']);
            } else {
                /** @var User $newOwner */
                $newOwner = User::find($data['owner_id']);
                $role = Role::query()
                    ->where('name', PermissionRepositoryInterface::ROLE_ADMIN_NAME)
                    ->where('team_id', $team->id)
                    ->first();
                if ($newOwner && $role) {
                    setPermissionsTeamId($team->id);
                    $newOwner->syncRoles($role);
                } else {
                    unset($data['owner_id']);
                }
            }
        }
        $team->fill($data);
        $team->save();
        return $team->refresh();
    }
}
