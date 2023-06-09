<?php

namespace Database\Seeders;

use App\Models\ClientType;
use Illuminate\Database\Seeder;

class ClientTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ClientType::create(['name' => 'MALAIKA']);
        ClientType::create(['name' => 'MNAC']);
        ClientType::create(['name' => 'ARQUEONET']);
        ClientType::create(['name' => 'OTROS']);
    }
}
