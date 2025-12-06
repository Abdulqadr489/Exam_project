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
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();

            $table->text('option_text')->nullable();
            $table->string('media_url')->nullable();

            $table->boolean('is_correct')->default(false);
            $table->float('weight')->default(0);
            $table->text('explanation')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->index(['question_id', 'is_correct']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};
