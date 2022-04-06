<?php
namespace Go2Flow\SaasRegisterLogin\Models;

use Spatie\Translatable\HasTranslations;

class Referral extends \Go2Flow\SaasRegisterLogin\Models\AbstractModels\AbstractReferral
{
    use HasTranslations;

    public $translatable = ['name'];
}
