<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CurrencyService
{
    public function list()
    {
        return Currency::query()
            ->where('user_id', Auth::id())
            ->orderBy('name')
            ->paginate(20);
    }

    public function create(array $data): Currency
    {
        return DB::transaction(function () use ($data) {
            return Currency::create([
                'name' => $data['name'],
                'symbol' => $data['symbol'] ?? null,
                'description' => $data['description'] ?? null,
                'user_id' => Auth::id(),
            ]);
        });
    }

    public function update(Currency $currency, array $data): Currency
    {
        $currency->fill($data);
        $currency->save();
        return $currency;
    }

    public function delete(Currency $currency): void
    {
        $currency->delete();
    }
}

