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
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');               // Duolingo, IELTS, TOEFL…
            $table->string('code')->unique();     // duo, ielts, toefl…
            $table->string('scoring_strategy')->default('default');
            $table->text('description')->nullable();
            $table->json('meta')->nullable();     // special rules, difficulty…
            $table->timestamps();
            $table->softDeletes();

            $table->index(['name', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_types');
    }
};
