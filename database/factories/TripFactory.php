<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientType;
use App\Models\Language;
use App\Models\Trip;
use App\Models\TripState;
use App\Traits\Slugeable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class TripFactory extends Factory
{
    use Slugeable;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Trip::class;

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Trip $trip) {
            // ...
        })->afterCreating(function (Trip $trip) {
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
        $title = 'Tour por ' . fake()->country();
        return [
            'title'         => $title,
            'slug'          => $this->slugify($title),
            'description'   => fake()->paragraph(),
            'commentary'    => rand(0, 10) >= 6 ? fake()->paragraph() : null,
            'trip_state_id'      => TripState::all()->random()->id,
        ];
    }
}
