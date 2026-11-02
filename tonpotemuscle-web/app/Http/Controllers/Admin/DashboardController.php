<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bilan;
use App\Models\Exercise;
use App\Models\Program;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => $this->stats(),
            'recentPrograms' => Program::query()->latest()->limit(5)->get(),
            'recentWorkouts' => Workout::query()->latest()->limit(5)->get(),
            'recentMembers' => User::query()->members()->latest()->limit(8)->get(),
            'recentCompletions' => WorkoutLog::query()
                ->with(['member', 'workout'])
                ->where('abandoned', false)
                ->whereNotNull('completed_at')
                ->latest('completed_at')
                ->limit(8)
                ->get(),
            'recentBilans' => Bilan::query()->withCount('questions')->latest()->limit(5)->get(),
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
}
