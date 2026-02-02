<?php

namespace App\Http\Controllers;

use App\DTOs\CreateTransactionData;
use App\Http\Requests\Transactions\CreateTransactionRequest;
use App\Models\Account;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(private readonly TransactionService $service)
    {
    }

    public function store(CreateTransactionRequest $request): JsonResponse
    {
        $data = new CreateTransactionData(
            amountCents: (int) $request->integer('amount_cents'),
            accountId: (int) $request->integer('account_id'),
            categoryId: (int) $request->integer('category_id'),
            date: $request->input('date'),
            description: $request->input('description'),
            initial: (bool) $request->boolean('initial', false)
        );

        $account = Account::findOrFail($data->accountId);
        $this->authorize('update', $account);

        $transaction = $this->service->create($data);

        return response()->json([
            'id' => $transaction->id,
        ], 201);
    }
}

