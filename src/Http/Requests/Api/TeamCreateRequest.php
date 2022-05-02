<?php

namespace Go2Flow\SaasRegisterLogin\Http\Requests\Api;

use Go2Flow\SaasRegisterLogin\Rules\ValidVatNumber;
use Illuminate\Foundation\Http\FormRequest;

class TeamCreateRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user() !== null;
    }

    public function rules()
    {
        return [
            'email' => 'required',
            'name' => 'required',
            'billing_address' => 'required',
            'billing_address_line_2' => 'nullable',
            'billing_city' => 'required',
            'billing_state' => 'nullable',
            'billing_postal_code' => 'required',
            'vat_id' => ['nullable', 'max:225', new ValidVatNumber()],
            'billing_country' => 'required',
            'currency' => 'required',
            'languages' => 'required',
        ];
    }
}
