<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RoomType::create(['name' => 'Dui', 'capacity' => 1]);
        RoomType::create(['name' => 'Doble', 'capacity' => 2]);
        RoomType::create(['name' => 'Twin', 'capacity' => 2]);
        RoomType::create(['name' => 'Triple', 'capacity' => 3]);
    }
}
