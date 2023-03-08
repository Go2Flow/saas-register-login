<?php
namespace Go2Flow\SaasRegisterLogin\Models\Team;

use Spatie\Permission\Models\Role;

class Invitation extends \Go2Flow\SaasRegisterLogin\Models\Team\AbstractModels\AbstractInvitation
{
    public $table = 'team_invitations';

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
