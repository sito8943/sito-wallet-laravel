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

    public function update(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            $transaction->load(['account', 'category']);

            $oldAccount = $transaction->account;
            $oldCategory = $transaction->category;
            $oldAmount = $transaction->amount_cents;

            $newAccount = $oldAccount;
            if (isset($data['account_id'])) {
                $newAccount = Account::findOrFail($data['account_id']);
                if ($newAccount->user_id !== Auth::id()) {
                    throw new InvalidArgumentException('Unauthorized account access');
                }
            }

            $newCategory = $oldCategory;
            if (isset($data['category_id'])) {
                $newCategory = TransactionCategory::findOrFail($data['category_id']);
                if ($newCategory->user_id !== $newAccount->user_id) {
                    throw new InvalidArgumentException('Category does not belong to user');
                }
            }

            $newAmount = $data['amount_cents'] ?? $oldAmount;

            // Reverse old effect
            $reversedBalance = $this->applyToBalance(
                $oldAccount->balance_cents,
                $oldCategory->type,
                $oldAmount
            );
            // reverse means subtract for IN, add for OUT -> we can reuse by flipping type
            $reversedBalance = match ($oldCategory->type) {
                TransactionType::IN => $oldAccount->balance_cents - $oldAmount,
                TransactionType::OUT => $oldAccount->balance_cents + $oldAmount,
            };
            if ($reversedBalance < 0) {
                throw new InvalidArgumentException('balance.greaterThan0');
            }
            $oldAccount->balance_cents = $reversedBalance;

            // Apply new effect on possibly new account
            $appliedBalance = match ($newCategory->type) {
                TransactionType::IN => $newAccount->balance_cents + $newAmount,
                TransactionType::OUT => $newAccount->balance_cents - $newAmount,
            };
            if ($appliedBalance < 0) {
                throw new InvalidArgumentException('balance.greaterThan0');
            }
            $newAccount->balance_cents = $appliedBalance;

            // Persist account(s)
            $oldAccount->save();
            if ($newAccount->id !== $oldAccount->id) {
                $newAccount->save();
            }

            // Update transaction fields
            $transaction->fill(collect($data)->only([
                'amount_cents', 'date', 'description', 'initial'
            ])->toArray());
            if ($newAccount->id !== $transaction->account_id) {
                $transaction->account()->associate($newAccount);
            }
            if ($newCategory->id !== $transaction->category_id) {
                $transaction->category()->associate($newCategory);
            }
            $transaction->save();

            return $transaction;
        });
    }

    public function delete(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $transaction->load(['account', 'category']);
            $account = $transaction->account;
            $category = $transaction->category;

            $newBalance = match ($category->type) {
                TransactionType::IN => $account->balance_cents - $transaction->amount_cents,
                TransactionType::OUT => $account->balance_cents + $transaction->amount_cents,
            };
            if ($newBalance < 0) {
                throw new InvalidArgumentException('balance.greaterThan0');
            }

            $account->balance_cents = $newBalance;
            $account->save();

            $transaction->delete();
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
