<?php

namespace App\Http\Requests\Accounts;

use App\Enums\AccountType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $types = implode(',', array_map(fn($e)=>$e->value, AccountType::cases()));
        return [
            'type' => ["sometimes", "in:$types"],
            'name' => ['sometimes', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'currency_id' => ['sometimes', 'integer', 'exists:currencies,id'],
        ];
    }
}

