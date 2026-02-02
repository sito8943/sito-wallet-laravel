<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'amount_cents' => fake()->numberBetween(1, 100000),
            'initial' => fake()->boolean(10),
            'date' => fake()->optional()->dateTimeBetween('-3 months', 'now'),
            'description' => fake()->optional()->sentence(),
            'account_id' => Account::factory(),
            'category_id' => function (array $attributes) {
                // Ensure the category belongs to the same user as the account
                $accountId = $attributes['account_id'];
                return TransactionCategory::factory()->state(function () use ($accountId) {
                    $account = Account::find($accountId);
                    return ['user_id' => $account?->user_id];
                });
            },
        ];
    }
}

