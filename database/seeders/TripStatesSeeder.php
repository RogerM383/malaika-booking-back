<?php

namespace Database\Seeders;

use App\Models\ClientType;
use App\Models\TripState;
use Illuminate\Database\Seeder;

class TripStatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TripState::create(['name' => 'OPEN']);
        TripState::create(['name' => 'ARCHIVED']);
    }
}
