<?php

namespace Database\Seeders;

use App\Enums\TransactionType;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        TransactionCategory::firstOrCreate(
            ['name' => 'Salary', 'user_id' => $user->id],
            ['type' => TransactionType::IN, 'description' => 'Income from work', 'initial' => false]
        );
        TransactionCategory::firstOrCreate(
            ['name' => 'Groceries', 'user_id' => $user->id],
            ['type' => TransactionType::OUT, 'description' => 'Food and supermarkets', 'initial' => false]
        );
    }
}

