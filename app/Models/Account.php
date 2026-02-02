<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;
    protected $table = 'accounts';

    protected $fillable = [
        'type',
        'name',
        'description',
        'balance_cents',
        'currency_id',
        'user_id',
    ];

    protected $casts = [
        'type' => AccountType::class,
        'balance_cents' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    protected function balance(): Attribute
    {
        return Attribute::get(fn () => $this->balance_cents);
    }
}
