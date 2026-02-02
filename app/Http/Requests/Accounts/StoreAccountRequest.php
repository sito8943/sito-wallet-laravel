<?php

namespace App\Http\Requests\Accounts;

use App\Enums\AccountType;
use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $types = implode(',', array_map(fn($e)=>$e->value, AccountType::cases()));
        return [
            'type' => ["required", "in:$types"],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
        ];
    }
}

