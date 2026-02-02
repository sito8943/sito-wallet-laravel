<?php

namespace App\Http\Requests\Currencies;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'symbol' => ['nullable', 'string', 'max:5'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}

