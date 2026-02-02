<?php

namespace App\Http\Controllers;

use App\Http\Requests\Accounts\StoreAccountRequest;
use App\Http\Requests\Accounts\UpdateAccountRequest;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function __construct(private readonly AccountService $service) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->list());
    }

    public function store(StoreAccountRequest $request): JsonResponse
    {
        $account = $this->service->create($request->validated());
        return response()->json(['id' => $account->id], 201);
    }

    public function show(Account $account): JsonResponse
    {
        $this->authorize('view', $account);
        return response()->json($account->load('currency'));
    }

    public function update(UpdateAccountRequest $request, Account $account): JsonResponse
    {
        $this->authorize('update', $account);
        $updated = $this->service->update($account, $request->validated());
        return response()->json($updated);
    }

    public function destroy(Account $account): JsonResponse
    {
        $this->authorize('delete', $account);
        $this->service->delete($account);
        return response()->json([], 204);
    }

    public function common(): JsonResponse
    {
        $items = Account::query()
            ->where('user_id', Auth::id())
            ->with('currency:id,name,symbol,updated_at')
            ->orderBy('name')
            ->get(['id', 'name', 'currency_id', 'updated_at'])
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'name' => $a->name,
                    'updatedAt' => $a->updated_at,
                    'currency' => [
                        'id' => $a->currency?->id,
                        'name' => $a->currency?->name,
                        'symbol' => $a->currency?->symbol,
                        'updatedAt' => $a->currency?->updated_at,
                    ],
                ];
            });

        return response()->json($items);
    }
}
