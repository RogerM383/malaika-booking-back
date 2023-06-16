<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientType;
use App\Models\Departure;
use App\Models\DepartureState;
use App\Models\Language;
use App\Models\Trip;
use App\Models\TripState;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class DepartureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Departure::class;

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Departure $trip) {
            // ...
        })->afterCreating(function (Departure $trip) {
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
        $date1 = $this->faker->dateTimeBetween('+1 week', '+5 week' );
        $date2 = $this->faker->dateTimeBetween('+5 week', '+8 week' );

        return [
            'start' => $date1,
            'final' => $date2,
            'price' => fake()->randomFloat(2,0, 12000),
            'taxes' => fake()->randomFloat(2,0, 200),
            'pax_available' => fake()->randomNumber(2),
            'individual_supplement' => fake()->randomFloat(2, 0, 500),
            'state_id' => DepartureState::all()->random()->id,
            'commentary' => rand(0, 10) >= 8 ? fake()->paragraph() : null,
            'expedient' => fake()->randomNumber(4, true),
        ];
    }
}
