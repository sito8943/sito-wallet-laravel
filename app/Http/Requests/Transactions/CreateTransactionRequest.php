<?php

namespace App\Http\Requests\Transactions;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount_cents' => ['required', 'integer', 'min:1'],
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'category_id' => ['required', 'integer', 'exists:transaction_categories,id'],
            'date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'initial' => ['sometimes', 'boolean'],
        ];
    }
}

