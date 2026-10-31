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
        Schema::create('program_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('program_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('active');
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('live_count')->default(0);
            $table->timestamp('enrolled_at');
            $table->timestamp('abandoned_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'program_id']);
            $table->index(['program_id', 'points']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_enrollments');
    }
};
