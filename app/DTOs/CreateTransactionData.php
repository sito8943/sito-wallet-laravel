<?php

namespace App\DTOs;

class CreateTransactionData
{
    public function __construct(
        public readonly int $amountCents,
        public readonly int $accountId,
        public readonly int $categoryId,
        public readonly ?string $date,
        public readonly ?string $description,
        public readonly bool $initial = false,
    ) {
    }
}

