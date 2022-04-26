<?php

namespace Go2Flow\SaasRegisterLogin\Repositories;

use Carbon\Carbon;
use Go2Flow\SaasRegisterLogin\Models\Team\Invitation;
use Go2Flow\SaasRegisterLogin\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        $password = $data['password'];
        unset($data['password']);
        unset($data['password_confirm']);
        if (isset($data['newsletter']) && $data['newsletter']) {
            // @TODO: Add To Newsletter service
            unset($data['newsletter']);
        }
        unset($data['agb']);
        $data['terms_accepted_at'] = Carbon::now();
        $user = new User($data);
        $user->password = Hash::make($password);
        $user->save();
        return $user->refresh();
    }

    /**
     * @param array $data
     * @param string $email
     * @param bool|null $isEmailVerified
     * @return User
     */
    public function createUserWithoutTeam(array $data, string $email, ?bool $isEmailVerified = false): User
    {
        $data['email'] = $email;
        if ($isEmailVerified) {
            $data['email_verified_at'] = Carbon::now();
        }
        $user = $this->create($data);
        return $user->refresh();
    }

    /**
     * @param User $user
     * @param Invitation $invite
     * @return void
     */
    public function addUserToTeam(User $user, Invitation $invite):void
    {
        if (!$user->teams()->where('id', $invite->team_id)->first()) {
            $user->teams()->attach($invite->team_id);
            $role = Role::find($invite->role_id);
            $user->assignRole($role);
        }
        $invite->delete();
    }
}
