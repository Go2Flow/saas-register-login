<?php

namespace Go2Flow\SaasRegisterLogin\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user.salutation' => 'required',
            'user.firstname' => 'required',
            'user.lastname' => 'required',
            'user.email' => 'required',
            'user.password' => 'required|min:8',
            'user.password_confirm' => 'required|same:user.password',
            'user.referral_id' => 'required',
            'user.newsletter' => 'nullable',
            'user.agb' => 'required',
            'team.name' => 'required',
            'team.billing_address' => 'required',
            'team.billing_address_line_2' => 'nullable',
            'team.billing_city' => 'required',
            'team.billing_state' => 'nullable',
            'team.billing_postal_code' => 'required',
            'team.vat_id' => 'nullable',
            'team.billing_country' => 'required',
            'team.currency' => 'required',
            'team.languages' => 'required',
        ];
    }

}
