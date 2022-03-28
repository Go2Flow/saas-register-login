<?php

namespace Go2Flow\SaasRegisterLogin\Repositories;

use Carbon\Carbon;
use Go2Flow\SaasRegisterLogin\Models\User;
use Illuminate\Support\Facades\Hash;

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
}
