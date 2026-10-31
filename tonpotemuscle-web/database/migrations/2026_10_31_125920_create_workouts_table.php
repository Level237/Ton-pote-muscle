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
        Schema::create('workouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('difficulty')->nullable();
            $table->unsignedInteger('total_duration_seconds')->default(0);
            $table->string('thumbnail_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
