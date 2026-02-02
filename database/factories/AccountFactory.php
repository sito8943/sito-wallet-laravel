<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(AccountType::cases()),
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'balance_cents' => fake()->numberBetween(0, 500000),
            'user_id' => User::factory(),
            'currency_id' => Currency::factory()->state(function (array $attributes) {
                return [ 'user_id' => $attributes['user_id'] ];
            }),
        ];
    }

    public function zeroBalance(): static
    {
        return $this->state(fn () => [ 'balance_cents' => 0 ]);
    }
}

