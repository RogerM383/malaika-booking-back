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
        RoomType::create(['name' => 'Dui']);
        RoomType::create(['name' => 'Doble']);
        RoomType::create(['name' => 'Twin']);
        RoomType::create(['name' => 'Triple']);
    }
}
