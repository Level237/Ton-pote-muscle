<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workout_logs', function (Blueprint $table) {
            $table->string('share_token', 32)->nullable()->unique();
        });

        Schema::table('ranking_snapshot_entries', function (Blueprint $table) {
            $table->string('share_token', 32)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('workout_logs', function (Blueprint $table) {
            $table->dropUnique(['share_token']);
            $table->dropColumn('share_token');
        });

        Schema::table('ranking_snapshot_entries', function (Blueprint $table) {
            $table->dropUnique(['share_token']);
            $table->dropColumn('share_token');
        });
    }
};
