<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Go2Flow\SaasRegisterLogin\Models\Referral;
use function session;

class ReferralController extends Controller
{
    public function getReferralOptions()
    {
        $lang = session()->get('locale', 'en');
        $options = [];
        /** @var Referral $referral */
        foreach (Referral::all() as $referral) {
            if (array_key_exists($lang, $referral->name)) {
                $name = $referral->name[$lang];
            } else {
                $name = $referral->name[key($referral->name)];
            }
            $options[] = [
              'value' => $referral->id,
              'label' => $name,
            ];
        }
        return $options;
    }
}
