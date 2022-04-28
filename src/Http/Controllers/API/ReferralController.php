<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers\API;

use Go2Flow\SaasRegisterLogin\Http\Controllers\Controller;
use Go2Flow\SaasRegisterLogin\Models\Referral;

class ReferralController extends Controller
{
    public function getReferralOptions()
    {
        $lang = session()->get('locale', 'en');
        $options = [];
        /** @var Referral $referral */
        foreach (Referral::all() as $referral) {
            $options[] = [
              'value' => $referral->id,
              'label' => $referral->name,
            ];
        }
        return $options;
    }
}
