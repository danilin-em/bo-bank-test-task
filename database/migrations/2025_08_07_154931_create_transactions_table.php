<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_account_id')
                ->nullable()
                ->constrained('accounts')
                ->onDelete('cascade');
            $table->foreignId('to_account_id')
                ->nullable()
                ->constrained('accounts')
                ->onDelete('cascade');
            $table->bigInteger('amount', false, true);
            $table->enum('type', ['deposit', 'transfer']);
            $table->string('status')->default('completed');
            $table->string('reference_id')->unique();
            $table->timestamps();

            // Индексы
            $table->index('from_account_id');
            $table->index('to_account_id');
            $table->index('reference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
