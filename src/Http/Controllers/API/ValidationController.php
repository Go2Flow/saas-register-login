<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers\API;

use Go2Flow\SaasRegisterLogin\Http\Controllers\Controller;
use Go2Flow\SaasRegisterLogin\Rules\ValidCountry;
use Go2Flow\SaasRegisterLogin\Rules\ValidVatNumber;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    /**
     * @param Request $request
     * @return bool
     */
    public function validateVatId(Request $request)
    {
        $request->validate([
            'vat_id' => ['nullable', 'max:225', new ValidVatNumber()]
        ]);
        return true;
    }

    public function validateCountry(Request $request)
    {
        $request->validate([
            'country' => ['required', 'max:2', new ValidCountry()]
        ]);
        return true;
    }

    public function validateUniqueTeamName(Request $request)
    {
        $request->validate([
            'name' => ['unique:Go2Flow\SaasRegisterLogin\Models\Team,name']
        ]);
        return true;
    }

    public function validateUniqueUserEmail(Request $request)
    {
        $request->validate([
            'email' => ['unique:Go2Flow\SaasRegisterLogin\Models\User,email']
        ]);
        return true;
    }
}
