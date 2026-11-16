<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHydrationGoalRequest;
use App\Models\User;
use App\Support\MemberHydration;
use Illuminate\Http\RedirectResponse;

class HydrationController extends Controller
{
    public function update(UpdateHydrationGoalRequest $request, User $user): RedirectResponse
    {
        abort_unless($user->isMember(), 404);

        MemberHydration::setGoal($user, (int) $request->validated('hydration_goal_glasses'));

        return redirect()
            ->to(route('admin.enrollments.show', ['user' => $user, 'suivi' => 'eau']).'#suivi')
            ->with('status', 'Objectif d’hydratation mis à jour.');
    }
}
