<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->timestamps();
        });

        Schema::table('workouts', function (Blueprint $table) {
            $table->foreignUuid('category_id')->nullable()->after('title')->constrained('workout_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('workouts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });

        Schema::dropIfExists('workout_categories');
    }
};
