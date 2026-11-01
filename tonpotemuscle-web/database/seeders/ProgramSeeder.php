<?php

namespace Database\Seeders;

use App\Enums\ProgramStatus;
use App\Models\Program;
use App\Models\Workout;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $byTitle = Workout::query()->pluck('id', 'title');

        Program::query()->whereIn('title', [
            'Remise en forme 4 semaines',
            'Force maison 6 semaines',
            'HIIT express 3 semaines',
        ])->each(function (Program $program): void {
            $program->title = match ($program->title) {
                'Remise en forme 4 semaines' => 'Remise en forme',
                'Force maison 6 semaines' => 'Force maison',
                'HIIT express 3 semaines' => 'HIIT express',
                default => $program->title,
            };
            $program->save();
        });

        $programs = [
            [
                'title' => 'Remise en forme',
                'description' => 'Programme d’une semaine pour démarrer ou reprendre, 3 séances.',
                'objective' => 'Condition générale',
                'level' => 'beginner',
                'status' => ProgramStatus::Open,
                'image_path' => 'programs/remise-en-forme.webp',
                'starts_on' => now()->startOfWeek()->toDateString(),
                'ends_on' => now()->startOfWeek()->addDays(6)->toDateString(),
                'days' => [
                    1 => 'Full body débutant',
                    3 => 'Gainage & core',
                    5 => 'Mobilité & activation',
                ],
            ],
            [
                'title' => 'Force maison',
                'description' => 'Focus force avec haltères et poids du corps, 4 séances sur une semaine.',
                'objective' => 'Force',
                'level' => 'intermediate',
                'status' => ProgramStatus::Open,
                'image_path' => 'programs/force-maison.webp',
                'starts_on' => now()->startOfWeek()->toDateString(),
                'ends_on' => now()->startOfWeek()->addDays(6)->toDateString(),
                'days' => [
                    1 => 'Haut du corps force',
                    2 => 'Jambes & fessiers',
                    4 => 'Push / Pull maison',
                    5 => 'Épaules & posture',
                ],
            ],
            [
                'title' => 'HIIT express',
                'description' => 'Courtes séances intenses pour le cardio, sur une semaine.',
                'objective' => 'Perte de gras / cardio',
                'level' => 'advanced',
                'status' => ProgramStatus::Draft,
                'image_path' => 'programs/hiit-express.webp',
                'starts_on' => now()->startOfWeek()->toDateString(),
                'ends_on' => now()->startOfWeek()->addDays(6)->toDateString(),
                'days' => [
                    1 => 'HIIT 20 minutes',
                    3 => 'Gainage & core',
                    5 => 'HIIT 20 minutes',
                ],
            ],
        ];

        foreach ($programs as $def) {
            $days = $def['days'];
            unset($def['days']);

            $program = Program::query()->firstOrCreate(
                ['title' => $def['title']],
                array_merge($def, [
                    'id' => (string) Str::uuid(),
                    'closed_at' => null,
                ])
            );

            $program->update(array_merge($def, ['closed_at' => null]));

            $program->schedule()->delete();
            foreach ($days as $day => $workoutTitle) {
                $workoutId = $byTitle[$workoutTitle] ?? null;
                if (! $workoutId) {
                    throw new \RuntimeException("Séance manquante pour {$def['title']}: {$workoutTitle}");
                }

                $program->schedule()->create([
                    'workout_id' => $workoutId,
                    'day_number' => $day,
                ]);
            }

            for ($day = 1; $day <= 7; $day++) {
                if (isset($days[$day])) {
                    continue;
                }

                $program->schedule()->create([
                    'workout_id' => null,
                    'day_number' => $day,
                ]);
            }
        }
    }
}
