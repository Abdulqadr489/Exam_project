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
                'single_choice',
                'multiple_choice',
                'text',
                'essay',
                'audio_mc',
                'video_response',
                'fill_blank',
                'matching',
            ])->default('single_choice');

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
