<?php

namespace App\Services;

use App\DTOs\CreateTransactionData;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransactionService
{
    public function create(CreateTransactionData $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $account = Account::query()->findOrFail($data->accountId);
            $category = TransactionCategory::query()->findOrFail($data->categoryId);

            if ($account->user_id !== Auth::id()) {
                throw new InvalidArgumentException('Unauthorized account access');
            }

            if ($category->user_id !== $account->user_id) {
                throw new InvalidArgumentException('Category does not belong to user');
            }

            $transaction = new Transaction([
                'amount_cents' => $data->amountCents,
                'initial' => $data->initial,
                'date' => $data->date,
                'description' => $data->description,
            ]);
            $transaction->account()->associate($account);
            $transaction->category()->associate($category);
            $transaction->save();

            $newBalance = $this->applyToBalance(
                $account->balance_cents,
                $category->type,
                $transaction->amount_cents
            );

            if ($newBalance < 0) {
                throw new InvalidArgumentException('balance.greaterThan0');
            }

            $account->balance_cents = $newBalance;
            $account->save();

            return $transaction;
        });
    }

    private function applyToBalance(int $currentBalanceCents, TransactionType $type, int $amountCents): int
    {
        return match ($type) {
            TransactionType::IN => $currentBalanceCents + $amountCents,
            TransactionType::OUT => $currentBalanceCents - $amountCents,
        };
    }
}

