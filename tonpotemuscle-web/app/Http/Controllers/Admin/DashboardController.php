<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\Program;
use App\Models\User;
use App\Models\Workout;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => $this->stats(),
            'quickLinks' => $this->quickLinks(),
            'recentPrograms' => Program::query()->latest()->limit(5)->get(),
            'recentWorkouts' => Workout::query()->latest()->limit(5)->get(),
            'recentMembers' => User::query()->members()->latest()->limit(5)->get(),
        ]);
    }

    /**
     * @return list<array{label: string, icon: string, value: int}>
     */
    private function stats(): array
    {
        return [
            [
                'label' => 'Membres',
                'icon' => 'fa-solid fa-user-group',
                'value' => User::query()->members()->count(),
            ],
            [
                'label' => 'Exercices',
                'icon' => 'fa-solid fa-dumbbell',
                'value' => Exercise::query()->count(),
            ],
            [
                'label' => 'Programmes',
                'icon' => 'fa-solid fa-calendar-check',
                'value' => Program::query()->count(),
            ],
            [
                'label' => 'Programmes en cours',
                'icon' => 'fa-solid fa-bolt',
                'value' => Program::query()->inProgress()->count(),
            ],
        ];
    }

    /**
     * @return list<array{label: string, description: string, href: string}>
     */
    private function quickLinks(): array
    {
        return [
            [
                'label' => 'Exercices',
                'description' => 'Catalogue et médias',
                'href' => route('admin.exercises.index'),
            ],
            [
                'label' => 'Séances',
                'description' => 'Composition et durées',
                'href' => route('admin.workouts.index'),
            ],
            [
                'label' => 'Programmes',
                'description' => 'Périodes et calendriers',
                'href' => route('admin.programs.index'),
            ],
            [
                'label' => 'Inscrits',
                'description' => 'Participants par programme',
                'href' => route('admin.enrollments.index'),
            ],
            [
                'label' => 'Classements',
                'description' => 'Clôture et export CSV',
                'href' => route('admin.rankings.index'),
            ],
        ];
    }
}
