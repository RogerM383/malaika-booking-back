<?php

namespace Database\Seeders;

use App\Models\Passport;
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
                if (rand(0,10) <= 8) {
                    $client->passport()->save(Passport::factory()->make());
                }
            });
    }
}

