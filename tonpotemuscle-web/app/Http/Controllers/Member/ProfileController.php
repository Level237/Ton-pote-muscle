<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\UpdateProfileRequest;
use App\Support\MemberProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('member.profile.edit', [
            'member' => $request->user(),
            'genders' => MemberProfile::GENDERS,
            'goals' => MemberProfile::GOALS,
            'fitnessLevels' => MemberProfile::FITNESS_LEVELS,
            'activityLevels' => MemberProfile::ACTIVITY_LEVELS,
            'motivations' => MemberProfile::MOTIVATIONS,
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->profileAttributes());

        return redirect()
            ->route('member.profile.edit')
            ->with('status', 'Profil enregistré.');
    }
}
