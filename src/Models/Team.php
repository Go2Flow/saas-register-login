<?php
namespace Go2Flow\SaasRegisterLogin\Models;

use Go2Flow\SaasRegisterLogin\Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Team extends \Go2Flow\SaasRegisterLogin\Models\AbstractModels\AbstractTeam
{
    use HasFactory, HasApiTokens;

    /** @return TeamFactory */
    protected static function newFactory()
    {
        return TeamFactory::new();
    }
}
