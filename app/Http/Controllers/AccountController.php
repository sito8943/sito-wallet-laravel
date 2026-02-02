<?php

namespace App\Http\Controllers;

use App\Http\Requests\Accounts\StoreAccountRequest;
use App\Http\Requests\Accounts\UpdateAccountRequest;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;
use App\Http\Controllers\Concerns\ParsesFilters;

class AccountController extends Controller
{
    use ParsesFilters;

    public function __construct(private readonly AccountService $service) {}

    public function index(): JsonResponse
    {
        $filters = $this->parseFilters(RequestFacade::query('filters'));
        $q = Account::query()->where('user_id', Auth::id())->with('currency');
        $this->applyBasicFilters($q, $filters, ['userId' => 'user_id']);

        $pageSize = (int) (RequestFacade::query('pageSize', 20));
        $pageSize = $pageSize > 0 && $pageSize <= 200 ? $pageSize : 20;
        $page = (int) (RequestFacade::query('page', 1));
        $paginator = $q->orderByDesc('id')->paginate($pageSize, ['*'], 'page', $page);
        return response()->json($this->toQueryResult($paginator));
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
            ->get(['id', 'name', 'currency_id', 'updated_at']);

        return response()->json(\App\Http\Resources\AccountCommonResource::collection($items));
    }
}
