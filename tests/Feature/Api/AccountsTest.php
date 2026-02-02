<?php

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('rejects unauthenticated access to accounts', function () {
    $this->getJson('/api/accounts')->assertStatus(401);
    $this->postJson('/api/accounts', [])->assertStatus(401);
});

it('lists only the authenticated user accounts', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Sanctum::actingAs($user);

    // Accounts for both users
    Account::factory()->count(2)->for($user)->create();
    Account::factory()->count(3)->for($other)->create();

    $res = $this->getJson('/api/accounts')
        ->assertOk()
        ->assertJsonStructure(['items', 'currentPage', 'pageSize', 'totalElements', 'totalPages'])
        ->json();
    expect($res['totalElements'])->toBe(2);
    expect(collect($res['items'])->every(fn ($a) => $a['user_id'] === $user->id))->toBeTrue();
});

it('creates an account for the authenticated user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $currency = Currency::factory()->for($user)->create();

    $payload = [
        'type' => AccountType::CASH->value,
        'name' => 'My Wallet',
        'description' => 'Daily cash',
        'currency_id' => $currency->id,
    ];

    $resp = $this->postJson('/api/accounts', $payload)
        ->assertCreated()
        ->assertJsonStructure(['id'])
        ->json();

    $account = Account::query()->findOrFail($resp['id']);
    expect($account->user_id)->toBe($user->id);
    expect($account->balance_cents)->toBe(0);
    expect($account->currency_id)->toBe($currency->id);
});

it('returns common list for the user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    // Ensure accounts exist and belong to user with currency
    Account::factory()->count(2)->for($user)->create();

    $this->getJson('/api/accounts/common')
        ->assertJson(fn ($json) => $json->each(fn ($item) => $item
            ->hasAll(['id', 'name', 'updatedAt', 'currency'])
            ->has('currency', fn ($c) => $c->hasAll(['id', 'name', 'symbol', 'updatedAt']))
        ));
});
