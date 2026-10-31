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
        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('workout_id')->constrained()->restrictOnDelete();
            $table->foreignId('program_enrollment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('program_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('program_workout_id')->nullable()->constrained()->nullOnDelete();
            $table->string('mode');
            $table->date('performed_on');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('actual_duration_seconds')->nullable();
            $table->unsignedTinyInteger('difficulty_feedback')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('abandoned')->default(false);
            $table->unsignedInteger('points_awarded')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'workout_id', 'performed_on']);
            $table->index(['program_enrollment_id', 'performed_on']);
            $table->index(['user_id', 'performed_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_logs');
    }
};
