<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PlaceholderController extends Controller
{
    public function __invoke(): View
    {
        $pages = [
            'admin.exercises.index' => [
                'title' => 'Exercices',
                'icon' => 'fa-solid fa-dumbbell',
                'description' => 'Créer, modifier et archiver les exercices du catalogue.',
            ],
            'admin.workouts.index' => [
                'title' => 'Séances',
                'icon' => 'fa-solid fa-stopwatch',
                'description' => 'Composer les séances et ordonner leurs exercices.',
            ],
            'admin.programs.index' => [
                'title' => 'Programmes',
                'icon' => 'fa-solid fa-calendar-check',
                'description' => 'Période, calendrier de séances, URL publique à envoyer.',
            ],
            'admin.enrollments.index' => [
                'title' => 'Inscrits',
                'icon' => 'fa-solid fa-user-group',
                'description' => 'Membres inscrits à un programme.',
            ],
            'admin.rankings.index' => [
                'title' => 'Classements',
                'icon' => 'fa-solid fa-trophy',
                'description' => 'Classement provisoire, clôture, publication et export CSV.',
            ],
        ];

        $page = $pages[request()->route()->getName()] ?? [
            'title' => 'Admin',
            'icon' => 'fa-solid fa-layer-group',
            'description' => '',
        ];

        return view('admin.placeholder', $page);
    }
}
