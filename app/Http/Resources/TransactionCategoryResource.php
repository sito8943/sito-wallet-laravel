<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionCategoryResource extends JsonResource
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
            'type' => $this->type,
            'initial' => (bool) $this->initial,
            'user_id' => $this->user_id,
            'updated_at' => $this->updated_at,
            'deletedAt' => $this->deleted_at,
        ];
    }
}

