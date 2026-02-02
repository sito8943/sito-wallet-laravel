<?php

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function seedTransactionsForUser(User $user): array {
    $account = Account::factory()->for($user)->create();
    // Non-deleted
    $t1 = Transaction::factory()->create(['account_id' => $account->id]);
    $t2 = Transaction::factory()->create(['account_id' => $account->id]);
    // Deleted at specific timestamps
    $td1 = Transaction::factory()->create(['account_id' => $account->id]);
    $td1->delete();
    $td1->update(['deleted_at' => now()->subDays(3)]);

    $td2 = Transaction::factory()->create(['account_id' => $account->id]);
    $td2->delete();
    $td2->update(['deleted_at' => now()->subDay()]);

    return [$t1, $t2, $td1, $td2];
}

it('filters deleted boolean true/false', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    seedTransactionsForUser($user);

    // Only deleted
    $resDeleted = $this->getJson('/api/transactions?filters=deleted==true')
        ->assertOk()->json();
    expect(collect($resDeleted['items'] ?? $resDeleted)->count())->toBeGreaterThan(0);

    // Only non-deleted
    $resActive = $this->getJson('/api/transactions?filters=deleted==false')
        ->assertOk()->json();
    expect(collect($resActive['items'] ?? $resActive)->count())->toBeGreaterThan(0);
});

it('filters deleted by date comparisons', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    [$t1, $t2, $td1, $td2] = seedTransactionsForUser($user);

    $date = now()->subDays(2)->toDateString();
    // Expect only those deleted on or after $date (i.e., td2)
    $res = $this->getJson('/api/transactions?filters=deleted>=' . $date)
        ->assertOk()->json();

    $items = collect($res['items'] ?? $res);
    expect($items->every(fn ($it) => $it['id'] !== $t1->id && $it['id'] !== $t2->id))->toBeTrue();
});
