<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->restrictOnDelete();
            $table->string('type');
            $table->foreignUuid('workout_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignUuid('program_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedInteger('program_week')->nullable();
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'starts_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_assignments');
    }
};
