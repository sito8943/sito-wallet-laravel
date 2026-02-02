<?php

namespace Database\Seeders;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        $account = Account::where('user_id', $user->id)->first();
        $income = TransactionCategory::where('user_id', $user->id)->where('type', TransactionType::IN)->first();
        if ($account && $income) {
            Transaction::firstOrCreate(
                [
                    'description' => 'Initial funding',
                    'account_id' => $account->id,
                    'category_id' => $income->id,
                ],
                [
                    'amount_cents' => 10000,
                    'initial' => true,
                    'date' => now()->subDay(),
                ]
            );
        }
    }
}

