<?php

namespace Go2Flow\SaasRegisterLogin\Repositories;

use Go2Flow\PSPClient\Services\Go2FlowFinance\Constants;
use Go2Flow\PSPClient\Services\Go2FlowFinance\G2FApiService;
use Go2Flow\PSPClient\Services\Go2FlowFinance\Models\Bank;
use Go2Flow\PSPClient\Services\Go2FlowFinance\Models\Merchant;
use Go2Flow\PSPClient\Services\Go2FlowFinance\Models\Personal;
use Go2Flow\SaasRegisterLogin\Events\TeamCreated;
use Go2Flow\SaasRegisterLogin\Mail\Invitation as InvitationMail;
use Go2Flow\SaasRegisterLogin\Mail\PaymentModelChange;
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
        $lang = $data['languages'];
        if (is_string($data['languages'])) {
            $data['languages'] = [$data['languages']];
        }

        $team = new Team($data);
        $team->save();
        $team->refresh();
        app(PermissionRepositoryInterface::class)->createBaseRoles($team);
        if ($owner) {
            $owner->teams()->syncWithoutDetaching([$team->id]);
            $role = Role::query()
                ->where('name', PermissionRepositoryInterface::ROLE_ADMIN_NAME)
                ->where('team_id', $team->id)
                ->first();
            if ($role) {
                setPermissionsTeamId($team->id);
                $owner->assignRole($role);
            }
        }
        event(new TeamCreated($team));
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
        if (isset($data['payment_model']) && $data['payment_model'] !== $team->payment_model) {
            Mail::to('support@courzly.com')->send(new PaymentModelChange($team));
        }
        $team->fill($data);
        session()->put($team->id.'_time_zone', $data['time_zone']);
        $team->save();
        return $team->refresh();
    }

    /**
     * @param Team $team
     * @param array $data
     * @return Team
     */
    public function updateBank(Team $team, array $data): Team
    {
        $team = $this->update($team, $data);
        if ($team->psp_id !== config('saas-register-login.dev_psp_id', '4053261b')) {
            $psp = new G2FApiService();
            $bank = new Bank();
            $bank->setMerchantId($team->psp_id)
                ->setCurrency($team->currency)
                ->setIban($team->bank_iban)
                ->setBic($team->bank_swift)
                ->setHolderName($team->name)
                ->setCountry($team->billing_country)
                ->setIsDefault();
            $psp->updateBank($bank);
        }
        return $team;
    }

    public function updateKycStatus(Team $team): void
    {
        if ($team->psp_id) {
            $psp = new G2FApiService();
            $status = $psp->getVerivication($team->psp_id);
            $team->kyc_status = $status;
            $team->save();
        }
    }

    /**
     * @param Team $team
     * @param string $instanceName
     * @return string|null
     */
    public function createPspMerchant(Team $team, string $instanceName): string|null
    {
        $psp = new G2FApiService();
        $merchant = new Merchant();
        $personal = new Personal();

        if(env('APP_ENV') == 'production') {
            $personal
                ->setCompany($team->name)
                ->setAddress($team->billing_address)
                ->setCity($team->billing_city)
                ->setCountry($team->billing_country)
                ->setZip($team->billing_postal_code)
                ->setSalutation($team->owner->salutation)
                ->setFirstName($team->owner->firstname)
                ->setLastName($team->owner->lastname)
                ->setVatNr($team->vat_id)
                ->setPhoneNumber($team->phone_number)
                ->setPhonePrefix($team->phone_prefix);

            $merchant
                ->setEmail($team->email)
                ->setMerchantData($personal)
                ->setSubdomain($instanceName)
                ->setReference('courzly-' . env('APP_ENV'))
                ->setLanguage($team->languages[0])
                ->setActivatePSP36(true)
                ->setSendWelcomeMail(false);

            $merchantResponse = $psp->createMerchant($merchant);
            if ($merchantResponse) {
                $psp->createWebhook($merchantResponse, config('saas-register-login.webhook', 'https://courzly.com/api/psp-client/go2flow/finance/payment/status'));
            }
            return $merchantResponse;
        } else {
            return config('saas-register-login.dev_psp_id', '4053261b');
        }
    }

    public function createPSPInstanceName(string $name, string $unique = ''):string
    {
        if(env('APP_ENV') == 'production') {
            return 'courzly-' . $unique . '-' . Str::slug($name);
        } else {
            return 'courzly-dev';
        }
    }
}
