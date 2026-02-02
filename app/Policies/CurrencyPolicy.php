<?php

namespace App\Policies;

use App\Models\Currency;
use App\Models\User;

class CurrencyPolicy
{
    public function view(User $user, Currency $currency): bool { return $currency->user_id === $user->id; }
    public function update(User $user, Currency $currency): bool { return $currency->user_id === $user->id; }
    public function delete(User $user, Currency $currency): bool { return $currency->user_id === $user->id; }
}

