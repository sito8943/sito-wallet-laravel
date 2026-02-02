<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Currency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AccountService
{
    public function list()
    {
        return Account::query()
            ->where('user_id', Auth::id())
            ->with(['currency'])
            ->orderBy('name')
            ->paginate(20);
    }

    public function create(array $data): Account
    {
        return DB::transaction(function () use ($data) {
            $currency = Currency::findOrFail($data['currency_id']);
            if ($currency->user_id !== Auth::id()) {
                throw new InvalidArgumentException('Currency does not belong to user');
            }

            return Account::create([
                'type' => $data['type'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'balance_cents' => 0,
                'currency_id' => $currency->id,
                'user_id' => Auth::id(),
            ]);
        });
    }

    public function update(Account $account, array $data): Account
    {
        if (isset($data['currency_id'])) {
            $currency = Currency::findOrFail($data['currency_id']);
            if ($currency->user_id !== $account->user_id) {
                throw new InvalidArgumentException('Currency does not belong to user');
            }
        }

        $account->fill(collect($data)->except('balance_cents')->toArray());
        $account->save();
        return $account;
    }

    public function delete(Account $account): void
    {
        $account->delete();
    }
}

