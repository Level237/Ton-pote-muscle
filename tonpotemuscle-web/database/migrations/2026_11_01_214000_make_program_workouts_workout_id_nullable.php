<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_workouts', function (Blueprint $table) {
            $table->dropForeign(['workout_id']);
        });

        Schema::table('program_workouts', function (Blueprint $table) {
            $table->uuid('workout_id')->nullable()->change();
        });

        Schema::table('program_workouts', function (Blueprint $table) {
            $table->foreign('workout_id')->references('id')->on('workouts')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('program_workouts', function (Blueprint $table) {
            $table->dropForeign(['workout_id']);
        });

        Schema::table('program_workouts', function (Blueprint $table) {
            $table->uuid('workout_id')->nullable(false)->change();
        });

        Schema::table('program_workouts', function (Blueprint $table) {
            $table->foreign('workout_id')->references('id')->on('workouts')->restrictOnDelete();
        });
    }
};
