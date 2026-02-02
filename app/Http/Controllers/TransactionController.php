<?php

namespace App\Http\Controllers;

use App\DTOs\CreateTransactionData;
use App\Http\Requests\Transactions\CreateTransactionRequest;
use App\Http\Requests\Transactions\UpdateTransactionRequest;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct(private readonly TransactionService $service)
    {
    }

    public function index(): JsonResponse
    {
        $txs = Transaction::query()
            ->whereHas('account', fn($q) => $q->where('user_id', Auth::id()))
            ->with(['account:id,name', 'category:id,name,type'])
            ->orderByDesc('date')
            ->paginate(20);
        return response()->json($txs);
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

    public function show(Transaction $transaction): JsonResponse
    {
        $this->authorize('view', $transaction);
        return response()->json($transaction->load(['account', 'category']));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('update', $transaction);
        $updated = $this->service->update($transaction, $request->validated());
        return response()->json($updated);
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        $this->authorize('delete', $transaction);
        $this->service->delete($transaction);
        return response()->json([], 204);
    }
}
