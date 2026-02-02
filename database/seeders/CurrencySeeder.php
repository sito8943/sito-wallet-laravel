<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        Currency::firstOrCreate(
            ['name' => 'USD', 'user_id' => $user->id],
            ['symbol' => '$', 'description' => 'US Dollar']
        );
        Currency::firstOrCreate(
            ['name' => 'EUR', 'user_id' => $user->id],
            ['symbol' => '€', 'description' => 'Euro']
        );
    }
}

