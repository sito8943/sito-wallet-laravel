<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('amount_cents');
            $table->boolean('initial')->default(false);
            $table->dateTime('date')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('transaction_categories')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['account_id', 'category_id']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

