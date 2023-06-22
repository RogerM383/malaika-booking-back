<?php

namespace Database\Seeders;

use App\Models\DepartureState;
use App\Models\Trip;
use Illuminate\Database\Seeder;

class TripsSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        Trip::factory()
            ->count(35)
            ->create()
            ->each(function ($trip) {
                $numberOfDepartures = rand(1, 4);
                $date1 = fake()->dateTimeBetween('+1 week', '+5 week' );
                $date2 = fake()->dateTimeBetween('+5 week', '+8 week' );

                $pax_capacity = fake()->numberBetween(10, 50);

                for ($i = 0; $i < $numberOfDepartures; $i++) {
                    $trip->departures()->create([
                        'start' => $date1,
                        'final' => $date2,
                        'price' => fake()->randomFloat(2,0, 12000),
                        'taxes' => fake()->randomFloat(2,0, 200),
                        'pax_capacity' => $pax_capacity,
                        'individual_supplement' => fake()->randomFloat(2, 0, 500),
                        'state_id' => DepartureState::all()->random()->id,
                        'commentary' => rand(0, 10) >= 8 ? fake()->paragraph() : null,
                        'expedient' => fake()->randomNumber(4, true),
                    ]);
                }

                $trip->departures()->each(function ($departure) use ($pax_capacity) {

                    if (rand(0, 10) >= 3) {
                        $dui = mt_rand(0, $pax_capacity);
                        if (($pax_capacity - $dui) % 2 !== 0 && $dui < $pax_capacity) {
                            $dui++;
                        }
                        $twin = mt_rand(0, $pax_capacity - $dui);
                        $doble = $pax_capacity - $dui - $twin;
                    } else {
                        $dui = $twin = $doble = null;
                    }

                    $departure->roomTypes()->attach([
                        1 => ['quantity' => $dui],
                        2 => ['quantity' => $twin],
                        3 => ['quantity' => $doble],
                        4 => ['quantity' => 0], // Triples fora
                    ]);
                });
            });
    }
}

