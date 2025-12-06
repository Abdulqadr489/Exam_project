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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();

            $table->string('fib_payment_id')->nullable();
            $table->string('fib_transaction_id')->nullable();

            $table->integer('amount');
            $table->enum('status', ['UNPAID', 'PAID', 'FAILED'])->default('UNPAID');

            $table->json('raw_request')->nullable();
            $table->json('raw_response')->nullable();
            $table->json('callback_data')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'exam_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
