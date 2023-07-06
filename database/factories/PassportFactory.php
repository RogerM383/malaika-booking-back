<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientType;
use App\Models\Language;
use App\Models\Passport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Mockery\Generator\StringManipulation\Pass\Pass;

/**
 * @extends Factory
 */
class PassportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Passport::class;

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Passport $boardgame) {
            // ...
        })->afterCreating(function (Passport $boardgame) {
            // ...
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $startDate = '2020-01-01';
        $endDate = '2023-12-31';
        $randomDate = fake()->dateTimeBetween($startDate, $endDate);

        $startDate2 = '1900-01-01';
        $endDate2 = '2000-12-31';
        $randomDate2 = fake()->dateTimeBetween($startDate2, $endDate2);

        return [
            'number_passport'   => fake()->regexify('[A-Z]{2}[0-9]{7}'),
            'nationality'       => 'Española',
            'issue'             => $randomDate,
            'exp'               => $randomDate,
            'birth'             => $randomDate2,
        ];
    }
}
