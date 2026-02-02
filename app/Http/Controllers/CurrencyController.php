<?php

namespace App\Http\Controllers;

use App\Http\Requests\Currencies\StoreCurrencyRequest;
use App\Http\Requests\Currencies\UpdateCurrencyRequest;
use App\Models\Currency;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;
use App\Http\Controllers\Concerns\ParsesFilters;

class CurrencyController extends Controller
{
    use ParsesFilters;
    public function __construct(private readonly CurrencyService $service) {}

    public function index(): JsonResponse
    {
        $filtersStr = RequestFacade::query('filters');
        $filters = $this->parseFilters($filtersStr);

        $q = Currency::query()->where('user_id', Auth::id());
        // map: userId -> user_id, id stays, name stays
        $this->applyBasicFilters($q, $filters, ['userId' => 'user_id']);

        $items = $q->orderByDesc('id')->get(['id', 'name', 'symbol', 'updated_at']);
        return response()->json(\App\Http\Resources\CurrencyResource::collection($items));
    }

    public function store(StoreCurrencyRequest $request): JsonResponse
    {
        $currency = $this->service->create($request->validated());
        return response()->json(['id' => $currency->id], 201);
    }

    public function show(Currency $currency): JsonResponse
    {
        $this->authorize('view', $currency);
        return response()->json(new \App\Http\Resources\CurrencyResource($currency));
    }

    public function update(UpdateCurrencyRequest $request, Currency $currency): JsonResponse
    {
        $this->authorize('update', $currency);
        $updated = $this->service->update($currency, $request->validated());
        return response()->json(new \App\Http\Resources\CurrencyResource($updated));
    }

    public function destroy(Currency $currency): JsonResponse
    {
        $this->authorize('delete', $currency);
        $this->service->delete($currency);
        return response()->json([], 204);
    }

    public function common(): JsonResponse
    {
        $items = Currency::query()
            ->where('user_id', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name', 'symbol', 'updated_at']);

        return response()->json(\App\Http\Resources\CurrencyCommonResource::collection($items));
    }
}
