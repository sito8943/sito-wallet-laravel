<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Policies\AccountPolicy;
use App\Policies\CurrencyPolicy;
use App\Policies\TransactionCategoryPolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Account::class, AccountPolicy::class);
        Gate::policy(Currency::class, CurrencyPolicy::class);
        Gate::policy(TransactionCategory::class, TransactionCategoryPolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);
    }
}
