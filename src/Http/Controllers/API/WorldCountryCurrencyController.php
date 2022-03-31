<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Nnjeim\World\Models\Currency;
use Nnjeim\World\World;
use function response;
use function session;

class WorldCountryCurrencyController extends Controller
{
    public function getCountryOptions()
    {
        $lang = session()->get('locale', 'en');
        $action = World::setLocale($lang)->countries(['fields' => 'name,iso2']);
        if ($action->success) {
            $options = [];
            foreach ($action->data as $country) {
                $options[] = ['value' => $country['iso2'], 'label' => $country['name']];
            }
            return $options;
        }
        return response('', 404);
    }

    public function getCurrencyOptions()
    {
        $currencies = Currency::query()->with('country')->get();
        $options = [];
        foreach ($currencies as $currency) {
            if (array_key_exists($currency->code, $options)) {
                $options[$currency->code]['country'][] = $currency->country->iso2;
            } else {
                $options[$currency->code] = ['value' => strtolower($currency->code), 'label' => $currency->name.' '.$currency->symbol, 'country' => [$currency->country->iso2]];
            }
        }
        return $options;
    }

    public function getLanguageOptions()
    {
        $lang = session()->get('locale', 'en');
        $action = World::setLocale($lang)->languages(['fields' => 'code,name']);
        if ($action->success) {
            $options = [];
            foreach ($action->data as $language) {
                $options[] = ['value' => strtolower($language['code']), 'label' => $language['name']];
            }
            return $options;
        }
        return response('', 404);
    }
}
