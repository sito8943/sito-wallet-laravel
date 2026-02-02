<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transactions';

    protected $fillable = [
        'amount_cents',
        'initial',
        'date',
        'description',
        'account_id',
        'category_id',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'initial' => 'boolean',
        'date' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }
}
