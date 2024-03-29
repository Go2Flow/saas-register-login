<?php

namespace Go2Flow\SaasRegisterLogin\Repositories;

use Go2Flow\SaasRegisterLogin\Models\Team;
use Go2Flow\SaasRegisterLogin\Models\User;

interface TeamRepositoryInterface
{
    /**
     * @param array $data
     * @param User|null $owner
     * @return Team
     */
    public function create(array $data, ?User $owner): Team;

    /**
     * @param Team $team
     * @param string $email
     * @param int|null $roleId
     * @return bool
     */
    public function invite(Team $team, string $email, ?int $roleId):bool;

    /**
     * @param Team $team
     * @param array $data
     * @return Team
     */
    public function update(Team $team, array $data):Team;

    /**
     * @param Team $team
     * @param array $data
     * @return Team
     */
    public function updateBank(Team $team, array $data):Team;

    /**
     * @param Team $team
     * @return void
     */
    public function updateKycStatus(Team $team): void;

    /**
     * @param string $name
     * @param string $unique
     * @return string
     */
    public function createPSPInstanceName(string $name, string $unique = ''):string;

    /**
     * @param Team $team
     * @param string $instanceName
     * @return string|null
     */
    public function createPspMerchant(Team $team, string $instanceName): string|null;
}
