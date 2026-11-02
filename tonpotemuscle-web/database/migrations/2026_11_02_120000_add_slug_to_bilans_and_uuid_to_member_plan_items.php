<?php

use App\Models\Bilan;
use App\Models\MemberPlanItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bilans', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        $known = [
            'Bilan de départ' => 'depart',
            'Bilan de santé' => 'sante',
            'Bilan de mesure' => 'mesure',
        ];

        Bilan::query()->withTrashed()->each(function (Bilan $bilan) use ($known): void {
            $bilan->forceFill([
                'slug' => $bilan->slug ?: ($known[$bilan->title] ?? Str::slug($bilan->title)),
            ])->save();
        });

        Schema::table('member_plan_items', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        MemberPlanItem::query()->each(function (MemberPlanItem $item): void {
            $item->forceFill([
                'uuid' => $item->uuid ?: (string) Str::uuid(),
            ])->save();
        });
    }

    public function down(): void
    {
        Schema::table('member_plan_items', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });

        Schema::table('bilans', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
