<?php

namespace Go2Flow\SaasRegisterLogin\Database\Factories;

use Go2Flow\SaasRegisterLogin\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Go2Flow\SaasRegisterLogin\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Team::class;

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
            'currency' => $this->faker->unique()->currencyCode(),
            'languages' => [ 'de', 'en', 'fr' ],
            'extra_billing_information' => $company,
            'billing_address' => $this->faker->streetAddress(),
            'billing_address_line_2' => 'additional_line_2 text',
            'billing_city' => $this->faker->city(),
            'billing_state' => 'Bavaria',
            'billing_postal_code' => $this->faker->postcode(),
            'billing_country' => $this->faker->countryCode(),
            'receipt_emails' => [$this->faker->unique()->safeEmail(),$this->faker->unique()->safeEmail()],
            'vat_id' => 'DE265215028'
        ];
    }

    private function generateRandomString($length = 10) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
}
