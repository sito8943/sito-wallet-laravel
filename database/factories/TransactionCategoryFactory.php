<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\TransactionCategory>
 */
class TransactionCategoryFactory extends Factory
{
    protected $model = TransactionCategory::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(TransactionType::cases()),
            'name' => fake()->unique()->word(),
            'description' => fake()->optional()->sentence(),
            'initial' => fake()->boolean(10),
            'user_id' => User::factory(),
        ];
    }
}

