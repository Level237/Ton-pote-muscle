<?php

namespace App\Http\Controllers\Member;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCoachMessageRequest;
use App\Models\User;
use App\Support\CoachChat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function show(Request $request): View
    {
        $member = $request->user();

        CoachChat::markRead($member, $member);

        $coach = User::query()->where('role', UserRole::Admin)->orderBy('id')->first();

        return view('member.messages.show', [
            'messages' => CoachChat::thread($member),
            'coachName' => $coach?->name ?: 'Ton coach',
        ]);
    }

    public function store(StoreCoachMessageRequest $request): RedirectResponse
    {
        $member = $request->user();

        CoachChat::send($member, $member, $request->validated('body'));

        return redirect()->route('member.messages.show');
    }
}
