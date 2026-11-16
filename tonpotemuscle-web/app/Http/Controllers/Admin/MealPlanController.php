<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GiveMealPlanRequest;
use App\Models\User;
use App\Support\MemberMeals;
use Illuminate\Http\RedirectResponse;

class MealPlanController extends Controller
{
    public function update(GiveMealPlanRequest $request, User $user): RedirectResponse
    {
        abort_unless($user->isMember() && ! $user->isDeactivated(), 404);

        MemberMeals::give($user, $request->user(), $request->validated());

        return redirect()
            ->to(route('admin.enrollments.show', ['user' => $user, 'suivi' => 'repas']).'#suivi')
            ->with('status', 'Repas donnés.');
    }
}
