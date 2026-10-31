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
        Schema::create('exercises', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('gif_path')->nullable();
            $table->string('video_url')->nullable();
            $table->json('body_parts')->nullable();
            $table->json('target_muscles')->nullable();
            $table->json('secondary_muscles')->nullable();
            $table->json('equipments')->nullable();
            $table->json('instructions')->nullable();
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
        Schema::dropIfExists('exercises');
    }
};
