<?php

namespace Database\Seeders;

use App\Models\DepartureState;
use Illuminate\Database\Seeder;

class DepartureStatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DepartureState::create(['name' => 'OPEN']);
        DepartureState::create(['name' => 'LOCKED']);
    }
}
