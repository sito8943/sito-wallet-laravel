<?php

namespace App\Policies;

use App\Models\TransactionCategory;
use App\Models\User;

class TransactionCategoryPolicy
{
    public function view(User $user, TransactionCategory $category): bool { return $category->user_id === $user->id; }
    public function update(User $user, TransactionCategory $category): bool { return $category->user_id === $user->id; }
    public function delete(User $user, TransactionCategory $category): bool { return $category->user_id === $user->id; }
}

