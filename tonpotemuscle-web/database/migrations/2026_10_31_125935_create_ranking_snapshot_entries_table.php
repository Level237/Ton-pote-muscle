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
        Schema::create('ranking_snapshot_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ranking_snapshot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('rank');
            $table->unsignedInteger('points');
            $table->unsignedInteger('live_count');
            $table->string('username');
            $table->timestamps();

            $table->unique(['ranking_snapshot_id', 'rank']);
            $table->unique(['ranking_snapshot_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranking_snapshot_entries');
    }
};
