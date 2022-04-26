<?php

namespace Go2Flow\SaasRegisterLogin\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TeamUpdateGeneralRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
            'owner_id' => 'required',
            'receipt_emails.*' => 'email',
            'billing_address' => 'required',
            'billing_address_line_2' => 'nullable',
            'billing_city' => 'required',
            'billing_state' => 'nullable',
            'billing_postal_code' => 'required',
            'billing_country' => 'required',
            'extra_billing_information' => 'nullable',
        ];
    }
}
