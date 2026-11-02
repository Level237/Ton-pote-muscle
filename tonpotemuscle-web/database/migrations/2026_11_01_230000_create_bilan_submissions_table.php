<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bilan_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('bilan_id')->constrained()->cascadeOnDelete();
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->unique(['user_id', 'bilan_id']);
        });

        Schema::create('bilan_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bilan_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bilan_question_id')->constrained()->cascadeOnDelete();
            $table->text('value');
            $table->timestamps();

            $table->unique(['bilan_submission_id', 'bilan_question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bilan_answers');
        Schema::dropIfExists('bilan_submissions');
    }
};
