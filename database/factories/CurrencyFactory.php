<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        $name = fake()->unique()->currencyCode();
        $symbolMap = [
            'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥', 'MXN' => '$',
        ];

        return [
            'name' => $name,
            'symbol' => $symbolMap[$name] ?? fake()->randomElement(['$', '€', '£', '¥', '₿']),
            'description' => fake()->optional()->sentence(),
            'user_id' => User::factory(),
        ];
    }
}

