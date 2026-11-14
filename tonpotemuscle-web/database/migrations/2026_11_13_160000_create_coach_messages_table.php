<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('from_member');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'id']);
            $table->index(['from_member', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_messages');
    }
};
