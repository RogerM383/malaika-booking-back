<?php

namespace Database\Factories;

use App\Models\Client;
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
            'place_birth'       => fake()->city()
        ];
    }
}
