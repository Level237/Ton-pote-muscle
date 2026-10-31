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
        if (! Schema::hasColumn('programs', 'thumbnail_path')) {
            return;
        }

        Schema::table('programs', function (Blueprint $table) {
            $table->renameColumn('thumbnail_path', 'image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('programs', 'image_path') || Schema::hasColumn('programs', 'thumbnail_path')) {
            return;
        }

        Schema::table('programs', function (Blueprint $table) {
            $table->renameColumn('image_path', 'thumbnail_path');
        });
    }
};
