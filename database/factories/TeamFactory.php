<?php

namespace Go2Flow\SaasRegisterLogin\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Go2Flow\SaasRegisterLogin\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $company = $this->faker->company();

        return [
            'psp_id' => $this->generateRandomString(64),
            'name' => $company,
            'email' => $this->faker->unique()->safeEmail(),
            'extra_billing_information' => $company,
            'billing_address' => $this->faker->streetAddress(),
            'billing_address_line_2' => 'additional_line_2 text',
            'billing_city' => $this->faker->city(),
            'billing_state' => 'Bavaria',
            'billing_postal_code' => $this->faker->postcode(),
            'billing_country' => $this->faker->countryCode(),
            'receipt_emails' => $this->faker->unique()->safeEmail().';'.$this->faker->unique()->safeEmail(),
            'vat_id' => 'DE265215028',
            'tax' => $this->faker->randomDigit()
        ];
    }

    private function generateRandomString($length = 10) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
}
