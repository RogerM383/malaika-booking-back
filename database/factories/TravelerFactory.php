<?php

namespace Database\Factories;

use App\Models\ClientType;
use App\Models\Traveler;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class TravelerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Traveler::class;

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Traveler $boardgame) {
            // ...
        })->afterCreating(function (Traveler $boardgame) {
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
        // $date = $this->faker->dateTimeBetween('-1 day' );
        return [
            'seat'          => 'PASSADIS',
            'observations'  => rand(1, 10) >= 5 ? fake()->sentence() : null,
            'intolerances'  => rand(1, 10) >= 9 ? fake()->word() : null,
            'client_type_id'   => ClientType::all()->random(),
            'frequency_fly' => null, // No que coño es esto, un ejemplo es esto "IBPLUS 26096982"
            'type_room'     => null, // Esto tasmbien es random, puede ser cualquier cosa, desde INDIVIDUAL hasta PREWFERCIA, PISO PRIMERO VENTANA BLABLABAL
            'notes'         => null, // Lo mismo que coño es esto? "CTC- CLARA MASSAGUER BARDAJI - 670659457"
            'member_number' => rand(1, 10) >= 6 ? "MNAC ". fake()->randomNumber(4) : null,
            'lang'          => rand(1, 10) >= 9 ? "CATALA" : null,
        ];
    }
}
