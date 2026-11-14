<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCoachMessageRequest;
use App\Models\User;
use App\Support\CoachChat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $unreadOnly = $request->boolean('unread');

        return view('admin.messages.index', [
            'members' => CoachChat::inbox($search, $unreadOnly),
            'search' => $search,
            'unreadOnly' => $unreadOnly,
        ]);
    }

    public function show(Request $request, User $user): View
    {
        abort_unless($user->isMember(), 404);

        CoachChat::markRead($user, $request->user());

        $search = $request->string('q')->trim()->toString();
        $unreadOnly = $request->boolean('unread');

        return view('admin.messages.show', [
            'member' => $user,
            'messages' => CoachChat::thread($user),
            'members' => CoachChat::inbox($search, $unreadOnly),
            'search' => $search,
            'unreadOnly' => $unreadOnly,
        ]);
    }

    public function store(StoreCoachMessageRequest $request, User $user): RedirectResponse
    {
        abort_unless($user->isMember(), 404);

        CoachChat::send($user, $request->user(), $request->validated('body'));

        return redirect()->route('admin.messages.show', $user);
    }
}
