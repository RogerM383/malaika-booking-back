<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::create(['name' => 'Català', 'code' => 'ca', 'locale' => 'ca_ES']);
        Language::create(['name' => 'Español', 'code' => 'es', 'locale' => 'es_ES']);
        Language::create(['name' => 'English', 'code' => 'en', 'locale' => 'en_GB']);
    }
}
