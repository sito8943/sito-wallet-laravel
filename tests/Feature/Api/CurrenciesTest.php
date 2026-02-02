<?php

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('rejects unauthenticated access to accounts', function () {
    $this->getJson('/api/currencies')->assertStatus(401);
    $this->postJson('/api/currencies', [])->assertStatus(401);
});

it('returns list for the user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    // Ensure currencies exist and belong to user
    Currency::factory()->count(2)->for($user)->create();

    $this->getJson('/api/currencies')
        ->assertJson(fn ($json) => $json
            ->hasAll(['items', 'currentPage', 'pageSize', 'totalElements', 'totalPages'])
            ->has('items', fn ($items) => $items->each(fn ($item) => $item
                ->hasAll(['id', 'symbol', 'name', 'updatedAt', 'deletedAt'])
            ))
        );
});

it('returns list for the user with filters', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    // Ensure currencies exist and belong to user
    Currency::factory()->count(2)->for($user)->create();

    $this->getJson('/api/currencies?filters=deleted==false,userId=='.$user->id)
        ->assertJson(fn ($json) => $json
            ->hasAll(['items', 'currentPage', 'pageSize', 'totalElements', 'totalPages'])
            ->has('items', fn ($items) => $items->each(fn ($item) => $item
                ->hasAll(['id', 'symbol', 'name', 'updatedAt', 'deletedAt'])
            ))
        );
});


it('returns common list for the user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    // Ensure currencies exist and belong to user
    Currency::factory()->count(2)->for($user)->create();

    $this->getJson('/api/currencies/common')
        ->assertJson(fn ($json) => $json->each(fn ($item) => $item
            ->hasAll(['id', 'symbol', 'name', 'updatedAt'])
        ));
});
