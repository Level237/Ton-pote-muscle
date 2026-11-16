<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('hydration_goal_glasses')->default(8)->after('equipment_notes');
        });

        Schema::create('hydration_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('day');
            $table->unsignedTinyInteger('glasses')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'day']);
            $table->index(['user_id', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hydration_logs');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('hydration_goal_glasses');
        });
    }
};
