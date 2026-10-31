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
        Schema::create('workout_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('workout_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('exercise_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('position')->default(1);
            $table->unsignedInteger('reps')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedInteger('rest_seconds')->default(30);
            $table->timestamps();

            $table->unique(['workout_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_exercises');
    }
};
