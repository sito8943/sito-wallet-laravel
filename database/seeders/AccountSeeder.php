<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        $currency = Currency::firstOrCreate(
            ['name' => 'USD', 'user_id' => $user->id],
            ['symbol' => '$', 'description' => 'US Dollar']
        );

        Account::firstOrCreate(
            ['name' => 'Main Wallet', 'user_id' => $user->id],
            [
                'type' => AccountType::CASH,
                'description' => 'Primary wallet account',
                'balance_cents' => 0,
                'currency_id' => $currency->id,
            ]
        );
    }
}

