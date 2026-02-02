<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountCommonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'updatedAt' => $this->updated_at,
            'currency' => [
                'id' => $this->currency?->id,
                'name' => $this->currency?->name,
                'symbol' => $this->currency?->symbol,
                'updatedAt' => $this->currency?->updated_at,
            ],
        ];
    }
}

