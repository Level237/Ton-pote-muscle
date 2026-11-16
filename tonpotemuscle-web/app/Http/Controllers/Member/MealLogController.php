<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMealCheckinRequest;
use App\Http\Requests\Member\StoreMealNoteRequest;
use App\Support\MemberMeals;
use Illuminate\Http\RedirectResponse;

class MealLogController extends Controller
{
    public function store(StoreMealCheckinRequest $request): RedirectResponse
    {
        MemberMeals::checkIn(
            $request->user(),
            $request->validated('meal'),
            $request->validated('status'),
        );

        return $this->backToMeals();
    }

    public function note(StoreMealNoteRequest $request): RedirectResponse
    {
        MemberMeals::note($request->user(), $request->validated('note'));

        return $this->backToMeals();
    }

    private function backToMeals(): RedirectResponse
    {
        return redirect()->to(route('dashboard', ['suivi' => 'repas']).'#suivi');
    }
}
