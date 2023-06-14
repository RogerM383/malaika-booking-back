<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientType;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Client $boardgame) {
            // ...
        })->afterCreating(function (Client $boardgame) {
            // ...
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name'              => fake()->firstName(),
            'surname'           => fake()->lastName(),
            'phone'             => fake()->phoneNumber(),
            'email'             => fake()->email(),
            'dni'               => fake()->dni(),
            'address'           => fake()->address(),
            'dni_expiration'    => fake()->date(),
            'place_birth'       => fake()->city(),
            'client_type_id'    => ClientType::all()->random()->id,
            'language_id'       => Language::all()->random()->id,

            'notes'             => rand(0, 10) >= 5 ? fake()->paragraph() : null,
            'intolerances'      => rand(0, 10) >= 7 ? join(' ,', fake()->words()) : null,
            'frequent_flyer'    => rand(0, 10) >= 8 ? fake()->randomNumber(5) : null,
            'member_number'     => rand(0, 10) >= 8 ? fake()->randomNumber(5) : null,
        ];
    }
}
