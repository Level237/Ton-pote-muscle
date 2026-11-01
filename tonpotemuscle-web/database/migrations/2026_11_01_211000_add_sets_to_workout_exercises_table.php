<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workout_exercises', function (Blueprint $table) {
            $table->unsignedInteger('sets')->default(1)->after('position');
        });
    }

    public function down(): void
    {
        Schema::table('workout_exercises', function (Blueprint $table) {
            $table->dropColumn('sets');
        });
    }
};
