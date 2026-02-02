<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionCategories\StoreTransactionCategoryRequest;
use App\Http\Requests\TransactionCategories\UpdateTransactionCategoryRequest;
use App\Models\TransactionCategory;
use App\Services\TransactionCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Enums\TransactionType as TypeEnum;

class TransactionCategoryController extends Controller
{
    public function __construct(private readonly TransactionCategoryService $service) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->list());
    }

    public function store(StoreTransactionCategoryRequest $request): JsonResponse
    {
        $category = $this->service->create($request->validated());
        return response()->json(['id' => $category->id], 201);
    }

    public function show(TransactionCategory $transactionCategory): JsonResponse
    {
        $this->authorize('view', $transactionCategory);
        return response()->json($transactionCategory);
    }

    public function update(UpdateTransactionCategoryRequest $request, TransactionCategory $transactionCategory): JsonResponse
    {
        $this->authorize('update', $transactionCategory);
        $updated = $this->service->update($transactionCategory, $request->validated());
        return response()->json($updated);
    }

    public function destroy(TransactionCategory $transactionCategory): JsonResponse
    {
        $this->authorize('delete', $transactionCategory);
        $this->service->delete($transactionCategory);
        return response()->json([], 204);
    }

    public function common(): JsonResponse
    {
        $items = TransactionCategory::query()
            ->where('user_id', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'initial', 'updated_at'])
            ->map(function ($c) {
                $type = $c->type instanceof TypeEnum ? $c->type : TypeEnum::from($c->type);
                $typeInt = match ($type) {
                    TypeEnum::OUT => 0,
                    TypeEnum::IN => 1,
                };
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'initial' => (bool) $c->initial,
                    'type' => $typeInt,
                    'updatedAt' => $c->updated_at,
                ];
            });

        return response()->json($items);
    }
}
