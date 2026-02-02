<?php

namespace App\Http\Controllers;

use App\Http\Requests\Currencies\StoreCurrencyRequest;
use App\Http\Requests\Currencies\UpdateCurrencyRequest;
use App\Models\Currency;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CurrencyController extends Controller
{
    public function __construct(private readonly CurrencyService $service) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->list());
    }

    public function store(StoreCurrencyRequest $request): JsonResponse
    {
        $currency = $this->service->create($request->validated());
        return response()->json(['id' => $currency->id], 201);
    }

    public function show(Currency $currency): JsonResponse
    {
        $this->authorize('view', $currency);
        return response()->json($currency);
    }

    public function update(UpdateCurrencyRequest $request, Currency $currency): JsonResponse
    {
        $this->authorize('update', $currency);
        $updated = $this->service->update($currency, $request->validated());
        return response()->json($updated);
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
            ->get(['id', 'name', 'symbol', 'updated_at'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'symbol' => $c->symbol,
                'updatedAt' => $c->updated_at,
            ]);

        return response()->json($items);
    }
}
