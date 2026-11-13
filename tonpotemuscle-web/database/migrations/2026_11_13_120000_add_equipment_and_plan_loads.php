<?php

use App\Enums\BilanQuestionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('trains_with_equipment')->nullable()->after('motivations');
            $table->text('equipment_notes')->nullable()->after('trains_with_equipment');
        });

        Schema::table('member_plan_items', function (Blueprint $table) {
            $table->json('loads')->nullable()->after('workout_id');
        });

        $questions = DB::table('bilan_questions')
            ->where('title', 'Matériel disponible')
            ->get();

        foreach ($questions as $question) {
            DB::table('bilan_questions')->where('id', $question->id)->update([
                'title' => 'Tu travailles avec du matériel ?',
                'type' => BilanQuestionType::Choice->value,
                'options' => json_encode(['Oui', 'Non']),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('member_plan_items', function (Blueprint $table) {
            $table->dropColumn('loads');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['trains_with_equipment', 'equipment_notes']);
        });
    }
};
