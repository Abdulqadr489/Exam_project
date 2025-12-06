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
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();

            $table->json('selected_option_ids')->nullable(); // array of option IDs
            $table->json('user_text_answer')->nullable();    // for essay / text

            $table->float('score_obtained')->default(0);

            $table->text('explanation')->nullable();         // “Your answer was correct but not best”
            $table->timestamps();
            $table->softDeletes();
            $table->index(['question_id','user_exam_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
