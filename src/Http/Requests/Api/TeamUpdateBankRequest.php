<?php

namespace Go2Flow\SaasRegisterLogin\Http\Requests\Api;

use Go2Flow\SaasRegisterLogin\Rules\ValidVatNumber;
use Illuminate\Foundation\Http\FormRequest;

class TeamUpdateBankRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'vat_id' => ['nullable', 'max:225', new ValidVatNumber()],
            'tax_number' => 'nullable',
            'bank_name' => 'nullable',
            'bank_iban' => 'nullable',
            'bank_swift' => 'nullable',
        ];
    }
}
