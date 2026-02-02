<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $base = [
            'id' => $this->id,
            'amount_cents' => $this->amount_cents,
            'initial' => (bool) $this->initial,
            'date' => $this->date,
            'description' => $this->description,
            'account_id' => $this->account_id,
            'category_id' => $this->category_id,
            'updated_at' => $this->updated_at,
            'deletedAt' => $this->deleted_at,
        ];

        if ($this->relationLoaded('account')) {
            $base['account'] = [
                'id' => $this->account?->id,
                'name' => $this->account?->name,
            ];
        }
        if ($this->relationLoaded('category')) {
            $base['category'] = [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'type' => $this->category?->type,
            ];
        }

        return $base;
    }
}

