<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionCategories\StoreTransactionCategoryRequest;
use App\Http\Requests\TransactionCategories\UpdateTransactionCategoryRequest;
use App\Models\TransactionCategory;
use App\Services\TransactionCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Enums\TransactionType as TypeEnum;
use Illuminate\Support\Facades\Request as RequestFacade;
use App\Http\Controllers\Concerns\ParsesFilters;

class TransactionCategoryController extends Controller
{
    use ParsesFilters;
    public function __construct(private readonly TransactionCategoryService $service) {}

    public function index(): JsonResponse
    {
        $filters = $this->parseFilters(RequestFacade::query('filters'));
        $user = auth()->user();
        $q = TransactionCategory::query()->where('user_id', $user->id);
        $this->applyBasicFilters($q, $filters, ['userId' => 'user_id']);

        // Map type filter if provided as 0/1
        foreach ($filters as [$field, $op, $value]) {
            if ($field === 'type' && $op === '==') {
                $q->where('type', ((int) $value) === 1 ? 'IN' : 'OUT');
            }
        }

        $pageSize = (int) (RequestFacade::query('pageSize', 20));
        $pageSize = $pageSize > 0 && $pageSize <= 200 ? $pageSize : 20;
        $page = ((int) (RequestFacade::query('page', 0))) + 1; // receive 0-based index
        $paginator = $q->orderByDesc('id')->paginate($pageSize, ['*'], 'page', $page);
        $items = collect($paginator->items())
            ->map(fn ($m) => (new \App\Http\Resources\TransactionCategoryResource($m))->toArray(request()));
        return response()->json([
            'items' => $items,
            'currentPage' => max(0, $paginator->currentPage() - 1),
            'pageSize' => $paginator->perPage(),
            'totalElements' => $paginator->total(),
            'totalPages' => $paginator->lastPage(),
        ]);
    }

    public function store(StoreTransactionCategoryRequest $request): JsonResponse
    {
        $category = $this->service->create($request->validated());
        return response()->json(['id' => $category->id], 201);
    }

    public function show(TransactionCategory $transactionCategory): JsonResponse
    {
        $this->authorize('view', $transactionCategory);
        return response()->json(new \App\Http\Resources\TransactionCategoryResource($transactionCategory));
    }

    public function update(UpdateTransactionCategoryRequest $request, TransactionCategory $transactionCategory): JsonResponse
    {
        $this->authorize('update', $transactionCategory);
        $updated = $this->service->update($transactionCategory, $request->validated());
        return response()->json(new \App\Http\Resources\TransactionCategoryResource($updated));
    }

    public function destroy(TransactionCategory $transactionCategory): JsonResponse
    {
        $this->authorize('delete', $transactionCategory);
        $this->service->delete($transactionCategory);
        return response()->json([], 204);
    }

    public function common(): JsonResponse
    {
        $user = auth()->user();
        $items = TransactionCategory::query()
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'initial', 'updated_at']);

        return response()->json(\App\Http\Resources\TransactionCategoryCommonResource::collection($items));
    }
}
