<?php

namespace App\Http\Requests\Transactions;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'amount_cents' => ['sometimes', 'integer', 'min:1'],
            'account_id' => ['sometimes', 'integer', 'exists:accounts,id'],
            'category_id' => ['sometimes', 'integer', 'exists:transaction_categories,id'],
            'date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'initial' => ['sometimes', 'boolean'],
        ];
    }
}

