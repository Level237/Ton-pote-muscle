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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('member')->after('id');
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('gender')->nullable()->after('email');
            $table->unsignedSmallInteger('height_cm')->nullable()->after('gender');
            $table->decimal('weight_kg', 5, 2)->nullable()->after('height_cm');
            $table->string('activity_level')->nullable()->after('weight_kg');
            $table->string('fitness_level')->nullable()->after('activity_level');
            $table->string('main_goal')->nullable()->after('fitness_level');
            $table->json('motivations')->nullable()->after('main_goal');

            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropUnique(['username']);
            $table->dropColumn([
                'role',
                'username',
                'gender',
                'height_cm',
                'weight_kg',
                'activity_level',
                'fitness_level',
                'main_goal',
                'motivations',
            ]);
        });
    }
};
