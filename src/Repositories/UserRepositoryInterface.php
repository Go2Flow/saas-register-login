<?php

namespace Go2Flow\SaasRegisterLogin\Repositories;

use Go2Flow\SaasRegisterLogin\Models\Team\Invitation;
use Go2Flow\SaasRegisterLogin\Models\User;

interface UserRepositoryInterface
{
    /**
     * @param array $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * @param array $data
     * @param string $email
     * @param bool|null $isEmailVerified
     * @return User
     */
    public function createUserWithoutTeam(array $data, string $email, ?bool $isEmailVerified = false): User;

    /**
     * @param User $user
     * @param Invitation $invite
     * @return void
     */
    public function addUserToTeam(User $user, Invitation $invite):void;
}
