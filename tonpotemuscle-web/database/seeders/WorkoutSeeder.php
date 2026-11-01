<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Workout;
use App\Models\WorkoutCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorkoutSeeder extends Seeder
{
    public function run(): void
    {
        $byTitle = Exercise::query()->pluck('id', 'title');

        $categories = collect(['Cardio', 'Force', 'Hypertrophie'])->mapWithKeys(function (string $title) {
            $category = WorkoutCategory::query()->firstOrCreate(
                ['title' => $title],
                ['id' => (string) Str::uuid()],
            );

            return [$title => $category->id];
        });

        $workouts = [
            [
                'title' => 'Full body débutant',
                'category' => 'Force',
                'difficulty' => 'beginner',
                'thumbnail_path' => 'workouts/full-body-debutant.webp',
                'items' => [
                    ['Pompes sur les genoux', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 45],
                    ['Squat au poids du corps', 'reps' => 12, 'duration_seconds' => null, 'rest_seconds' => 45],
                    ['Pont fessier', 'reps' => 12, 'duration_seconds' => null, 'rest_seconds' => 30],
                    ['Planche', 'reps' => null, 'duration_seconds' => 30, 'rest_seconds' => 30],
                    ['Jumping jacks', 'reps' => null, 'duration_seconds' => 40, 'rest_seconds' => 40],
                ],
            ],
            [
                'title' => 'Haut du corps force',
                'category' => 'Force',
                'difficulty' => 'intermediate',
                'thumbnail_path' => 'workouts/haut-du-corps.webp',
                'items' => [
                    ['Pompes', 'reps' => 12, 'duration_seconds' => null, 'rest_seconds' => 60],
                    ['Rowing haltères', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 60],
                    ['Développé militaire', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 60],
                    ['Dips sur banc', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 45],
                    ['Curl biceps', 'reps' => 12, 'duration_seconds' => null, 'rest_seconds' => 45],
                    ['Extension triceps', 'reps' => 12, 'duration_seconds' => null, 'rest_seconds' => 45],
                ],
            ],
            [
                'title' => 'Jambes & fessiers',
                'category' => 'Hypertrophie',
                'difficulty' => 'intermediate',
                'thumbnail_path' => 'workouts/jambes-fessiers.webp',
                'items' => [
                    ['Squat goblet', 'reps' => 12, 'duration_seconds' => null, 'rest_seconds' => 60],
                    ['Fentes avant', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 45],
                    ['Soulevé de terre roumain', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 60],
                    ['Hip thrust', 'reps' => 12, 'duration_seconds' => null, 'rest_seconds' => 45],
                    ['Élévations de mollets', 'reps' => 15, 'duration_seconds' => null, 'rest_seconds' => 30],
                ],
            ],
            [
                'title' => 'Gainage & core',
                'category' => 'Force',
                'difficulty' => 'beginner',
                'thumbnail_path' => 'workouts/gainage-core.webp',
                'items' => [
                    ['Planche', 'reps' => null, 'duration_seconds' => 40, 'rest_seconds' => 30],
                    ['Planche latérale', 'reps' => null, 'duration_seconds' => 30, 'rest_seconds' => 30],
                    ['Dead bug', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 30],
                    ['Oiseau-chien', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 30],
                    ['Crunch', 'reps' => 15, 'duration_seconds' => null, 'rest_seconds' => 30],
                ],
            ],
            [
                'title' => 'HIIT 20 minutes',
                'category' => 'Cardio',
                'difficulty' => 'advanced',
                'thumbnail_path' => 'workouts/hiit-20.webp',
                'items' => [
                    ['Jumping jacks', 'reps' => null, 'duration_seconds' => 40, 'rest_seconds' => 20],
                    ['Burpees', 'reps' => 8, 'duration_seconds' => null, 'rest_seconds' => 40],
                    ['Mountain climbers', 'reps' => null, 'duration_seconds' => 40, 'rest_seconds' => 20],
                    ['Squat au poids du corps', 'reps' => 15, 'duration_seconds' => null, 'rest_seconds' => 30],
                    ['Pompes', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 40],
                    ['Kettlebell swing', 'reps' => 15, 'duration_seconds' => null, 'rest_seconds' => 40],
                ],
            ],
            [
                'title' => 'Épaules & posture',
                'category' => 'Hypertrophie',
                'difficulty' => 'intermediate',
                'thumbnail_path' => 'workouts/epaules-posture.webp',
                'items' => [
                    ['Développé militaire', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 60],
                    ['Élévations latérales', 'reps' => 12, 'duration_seconds' => null, 'rest_seconds' => 45],
                    ['Face pull', 'reps' => 15, 'duration_seconds' => null, 'rest_seconds' => 45],
                    ['Oiseau-chien', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 30],
                    ['Planche', 'reps' => null, 'duration_seconds' => 40, 'rest_seconds' => 30],
                ],
            ],
            [
                'title' => 'Push / Pull maison',
                'category' => 'Force',
                'difficulty' => 'intermediate',
                'thumbnail_path' => 'workouts/push-pull.webp',
                'items' => [
                    ['Pompes', 'reps' => 12, 'duration_seconds' => null, 'rest_seconds' => 45],
                    ['Rowing haltères', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 45],
                    ['Pompes diamant', 'reps' => 8, 'duration_seconds' => null, 'rest_seconds' => 45],
                    ['Face pull', 'reps' => 12, 'duration_seconds' => null, 'rest_seconds' => 45],
                    ['Dips sur banc', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 45],
                ],
            ],
            [
                'title' => 'Mobilité & activation',
                'category' => 'Cardio',
                'difficulty' => 'beginner',
                'thumbnail_path' => 'workouts/mobilite.webp',
                'items' => [
                    ['Jumping jacks', 'reps' => null, 'duration_seconds' => 45, 'rest_seconds' => 20],
                    ['Oiseau-chien', 'reps' => 8, 'duration_seconds' => null, 'rest_seconds' => 20],
                    ['Pont fessier', 'reps' => 10, 'duration_seconds' => null, 'rest_seconds' => 20],
                    ['Planche', 'reps' => null, 'duration_seconds' => 20, 'rest_seconds' => 20],
                    ['Fentes arrière', 'reps' => 8, 'duration_seconds' => null, 'rest_seconds' => 30],
                ],
            ],
        ];

        foreach ($workouts as $def) {
            $items = $def['items'];
            $categoryId = $categories[$def['category']] ?? null;
            unset($def['items'], $def['category']);

            $total = 0;
            foreach ($items as $item) {
                $total += (int) ($item['duration_seconds'] ?? 0) + (int) $item['rest_seconds'];
            }

            $workout = Workout::query()->firstOrCreate(
                ['title' => $def['title']],
                [
                    'id' => (string) Str::uuid(),
                    'category_id' => $categoryId,
                    'difficulty' => $def['difficulty'],
                    'thumbnail_path' => $def['thumbnail_path'],
                    'total_duration_seconds' => $total,
                ]
            );

            $workout->update([
                'category_id' => $categoryId,
                'difficulty' => $def['difficulty'],
                'thumbnail_path' => $def['thumbnail_path'],
                'total_duration_seconds' => $total,
            ]);

            $workout->items()->delete();
            $position = 1;
            foreach ($items as $item) {
                $title = $item[0];
                $exerciseId = $byTitle[$title] ?? null;
                if (! $exerciseId) {
                    throw new \RuntimeException("Exercice manquant pour la séance {$def['title']}: {$title}");
                }

                $workout->items()->create([
                    'exercise_id' => $exerciseId,
                    'position' => $position++,
                    'reps' => $item['reps'],
                    'duration_seconds' => $item['duration_seconds'],
                    'rest_seconds' => $item['rest_seconds'],
                ]);
            }
        }
    }
}
