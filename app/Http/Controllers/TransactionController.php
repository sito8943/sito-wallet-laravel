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
use Illuminate\Support\Facades\Request as RequestFacade;
use App\Http\Controllers\Concerns\ParsesFilters;

class TransactionController extends Controller
{
    use ParsesFilters;
    public function __construct(private readonly TransactionService $service)
    {
    }

    public function index(): JsonResponse
    {
        $filters = $this->parseFilters(RequestFacade::query('filters'));
        $user = auth()->user();
        $q = Transaction::query()
            ->whereHas('account', fn ($q) => $q->where('user_id', $user->id))
            ->with(['account:id,name', 'category:id,name,type']);

        // Basic filters
        $this->applyBasicFilters($q, $filters, ['account' => 'account_id'], true);
        // Map type filter 0/1 into category.type IN/OUT
        foreach ($filters as [$field, $op, $value]) {
            if ($field === 'type' && $op === '==') {
                $q->whereHas('category', function ($c) use ($value) {
                    $c->where('type', ((int) $value) === 1 ? 'IN' : 'OUT');
                });
            }
        }

        $pageSize = (int) (RequestFacade::query('pageSize', 20));
        $pageSize = $pageSize > 0 && $pageSize <= 200 ? $pageSize : 20;
        $page = (int) (RequestFacade::query('page', 1));
        $paginator = $q->orderByDesc('date')->paginate($pageSize, ['*'], 'page', $page);
        return response()->json($this->toQueryResult($paginator));
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

    public function common(): JsonResponse
    {
        $user = auth()->user();
        $query = Transaction::query()
            ->whereHas('account', fn ($q) => $q->where('user_id', $user->id));

        // Optional filters: type and account
        $type = request()->query('type');
        $accountId = request()->query('account');
        if ($accountId) {
            $query->where('account_id', (int) $accountId);
        }
        if ($type !== null) {
            // 1 => IN, 0 => OUT
            $query->whereHas('category', function ($q) use ($type) {
                $q->where('type', ((int) $type) === 1 ? 'IN' : 'OUT');
            });
        }

        $items = $query
            ->orderByDesc('updated_at')
            ->limit(100)
            ->get(['id', 'updated_at']);

        return response()->json(\App\Http\Resources\TransactionCommonResource::collection($items));
    }
}
