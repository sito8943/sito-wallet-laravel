<?php

namespace App\Http\Resources;

use App\Enums\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionCategoryCommonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $type = $this->type instanceof TransactionType
            ? $this->type
            : TransactionType::from($this->type);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'initial' => (bool) $this->initial,
            'type' => match ($type) {
                TransactionType::IN => 1,
                TransactionType::OUT => 0,
            },
            'updatedAt' => $this->updated_at,
        ];
    }
}

