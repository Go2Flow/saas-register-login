<?php

namespace Go2Flow\SaasRegisterLogin\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserInviteAcceptRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [];
        if (request()->has('user')) {
            $rules = [
                'user.salutation' => 'required',
                'user.firstname' => 'required',
                'user.lastname' => 'required',
                'user.password' => 'required|min:8',
                'user.password_confirm' => 'required|same:user.password',
                'user.newsletter' => 'nullable',
                'user.agb' => 'required',
            ];
        }
        return $rules;
    }

}
