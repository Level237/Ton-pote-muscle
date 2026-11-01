<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_assignment_id')->constrained()->cascadeOnDelete();
            $table->date('planned_on');
            $table->string('kind');
            $table->foreignUuid('workout_id')->nullable()->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'planned_on']);
            $table->index(['user_id', 'planned_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_plan_items');
    }
};
