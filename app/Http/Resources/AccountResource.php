<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'type' => $this->type,
            'name' => $this->name,
            'description' => $this->description,
            'balance_cents' => $this->balance_cents,
            'currency_id' => $this->currency_id,
            'user_id' => $this->user_id,
            'updated_at' => $this->updated_at,
            'deletedAt' => $this->deleted_at,
        ];

        if ($this->relationLoaded('currency')) {
            $base['currency'] = [
                'id' => $this->currency?->id,
                'name' => $this->currency?->name,
                'symbol' => $this->currency?->symbol,
                'updatedAt' => $this->currency?->updated_at,
            ];
        }

        return $base;
    }
}

