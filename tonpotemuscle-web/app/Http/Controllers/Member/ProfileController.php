<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\UpdateProfileRequest;
use App\Support\MemberProfile;
use App\Support\MemberWeight;
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
        $member = $request->user();
        $previous = $member->weight_kg;
        $attributes = $request->profileAttributes();

        $member->update($attributes);

        $weight = $attributes['weight_kg'];
        if ($weight !== null && round((float) $weight, 1) !== round((float) $previous, 1)) {
            MemberWeight::record($member, $weight);
        }

        return redirect()
            ->route('member.profile.edit')
            ->with('status', 'Profil enregistré.');
    }
}
