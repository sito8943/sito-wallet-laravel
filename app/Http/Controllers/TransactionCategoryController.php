<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionCategories\StoreTransactionCategoryRequest;
use App\Http\Requests\TransactionCategories\UpdateTransactionCategoryRequest;
use App\Models\TransactionCategory;
use App\Services\TransactionCategoryService;
use Illuminate\Http\JsonResponse;

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
}

