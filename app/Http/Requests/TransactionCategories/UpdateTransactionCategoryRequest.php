<?php

namespace App\Http\Requests\TransactionCategories;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $types = implode(',', array_map(fn($e)=>$e->value, TransactionType::cases()));
        return [
            'type' => ["sometimes", "in:$types"],
            'name' => ['sometimes', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'initial' => ['sometimes', 'boolean'],
        ];
    }
}

