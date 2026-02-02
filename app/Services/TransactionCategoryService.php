<?php

namespace App\Services;

use App\Models\TransactionCategory;
use Illuminate\Support\Facades\Auth;

class TransactionCategoryService
{
    public function list()
    {
        return TransactionCategory::query()
            ->where('user_id', Auth::id())
            ->orderBy('name')
            ->paginate(20);
    }

    public function create(array $data): TransactionCategory
    {
        return TransactionCategory::create([
            'type' => $data['type'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'initial' => (bool) ($data['initial'] ?? false),
            'user_id' => Auth::id(),
        ]);
    }

    public function update(TransactionCategory $category, array $data): TransactionCategory
    {
        $category->fill($data);
        $category->save();
        return $category;
    }

    public function delete(TransactionCategory $category): void
    {
        $category->delete();
    }
}

