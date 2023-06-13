<?php

namespace Database\Seeders;

use App\Models\TripState;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RoomTypesSeeder::class,
            TripStatesSeeder::class,
            // trips seeder
            ClientTypesSeeder::class,
            ClientsSeeder::class,
        ]);
    }
}
