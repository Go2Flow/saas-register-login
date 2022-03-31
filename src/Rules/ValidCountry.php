<?php

namespace Go2Flow\SaasRegisterLogin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Nnjeim\World\World;

class ValidCountry implements Rule
{
    /**
     * @inheritDoc
     */
    public function passes($attribute, $value)
    {
        $lang = session()->get('locale', 'en');
        $action = World::setLocale($lang)->countries(['fields' => 'iso2']);
        $options = [];
        if ($action->success) {
            foreach ($action->data as $country) {
                $options[] = $country['iso2'];
            }
        }
        return in_array(
            $value,
            $options
        );
    }

    /**
     * @inheritDoc
     */
    public function message()
    {
        return __('The selected country is invalid.');
    }
}
