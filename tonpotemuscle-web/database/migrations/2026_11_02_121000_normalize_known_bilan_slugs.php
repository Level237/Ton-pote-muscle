<?php

use App\Models\Bilan;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $known = [
            'Bilan de départ' => 'depart',
            'Bilan de santé' => 'sante',
            'Bilan de mesure' => 'mesure',
        ];

        foreach ($known as $title => $slug) {
            Bilan::query()->withTrashed()->where('title', $title)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        //
    }
};
