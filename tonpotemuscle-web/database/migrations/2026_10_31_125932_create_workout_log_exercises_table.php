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
        Schema::create('workout_log_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workout_exercise_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('exercise_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('position');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->boolean('skipped')->default(false);
            $table->timestamps();

            $table->index(['workout_log_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_log_exercises');
    }
};
