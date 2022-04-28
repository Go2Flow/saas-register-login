<?php

namespace Go2Flow\SaasRegisterLogin\Repositories;

use Go2Flow\PSPClient\Services\Go2FlowFinance\Constants;
use Go2Flow\PSPClient\Services\Go2FlowFinance\G2FApiService;
use Go2Flow\PSPClient\Services\Go2FlowFinance\Models\Merchant;
use Go2Flow\PSPClient\Services\Go2FlowFinance\Models\Personal;
use Go2Flow\SaasRegisterLogin\Mail\Invitation as InvitationMail;
use Go2Flow\SaasRegisterLogin\Models\Team;
use Go2Flow\SaasRegisterLogin\Models\Team\Invitation;
use Go2Flow\SaasRegisterLogin\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

        $psp = new G2FApiService();
        $merchant = new Merchant();
        $personal = new Personal();

        $personal->setAddress($data['billing_address'])
            ->setCity($data['billing_city'])
            ->setZip($data['billing_zip'])
            ->setCompany($data['billing_company'])
            ->setCountry($data['billing_country'])
            ->setZip($data['billing_postal_code'])
            ->setFirstName($data['firstname'])
            ->setLastName($data['lastname'])
            ->setEmployees(Constants::EMPLOYEE[1])
            ->setFieldOfCompetence(Constants::COMPETENCE[1])
            ->setBusiness(Constants::BUSINESS[1])
            ->setLegalForm(Constants::LEGAL_FORM[1])
            ->setPhoneNumber('01737193481')
            ->setPhonePrefix('+49')
            ->setSalutation($data['salutation'])
            ->setVatNr($data['vat_id']);

        $merchant
            ->setEmail($data['team_name'])
            ->setMerchantData($personal)
            ->setSubdomain(Str::slug($data['']))
            ->setReference('courzly_'.env('APP_ENV'))
            ->setLanguage( $data['languages'])
            ->setActivatePSP36(true)
            ->setSendWelcomeMail(true);

        $psp->createMerchant($merchant);

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
