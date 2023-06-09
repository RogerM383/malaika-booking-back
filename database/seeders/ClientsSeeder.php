<?php

namespace Database\Seeders;

use App\Models\Traveler;
use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        Client::factory()
            ->count(50)
            ->create()
            ->each(function ($client) {
                $client->traveler()->save(Traveler::factory()->make());
            });
    }
}

