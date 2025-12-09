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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_section_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('question_type', [
                'single_choice',   // MCQ, one correct
                'multiple_choice', // MCQ, multiple correct
                'fill_blank',      // fill in the gaps
                'short_text',      // short written answer
                'long_text',       // essay / long writing
                'speaking',        // user records voice (repeat, describe, etc.)
                'listening',       // user listens, then answers (text or choice)
                'reorder',         // reorder words/sentences
                'matching',        // match pairs
            ]);

            $table->text('question_text');
            $table->string('media_url')->nullable();
            $table->integer('max_score')->default(1);

            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->index(['exam_section_id', 'question_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
