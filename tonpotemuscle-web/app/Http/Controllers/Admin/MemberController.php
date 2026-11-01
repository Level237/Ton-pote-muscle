<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $deactivated = $request->boolean('deactivated');
        $search = $request->string('q')->trim()->toString();

        $members = User::query()
            ->members()
            ->when(
                $deactivated,
                fn ($query) => $query->deactivated(),
                fn ($query) => $query->active(),
            )
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.members.index', [
            'members' => $members,
            'deactivated' => $deactivated,
            'search' => $search,
        ]);
    }

    public function deactivate(User $user): RedirectResponse
    {
        abort_unless($user->isMember() && ! $user->isDeactivated(), 404);

        $user->update(['deactivated_at' => now()]);

        return redirect()
            ->route('admin.enrollments.index')
            ->with('status', 'Compte désactivé.');
    }

    public function activate(User $user): RedirectResponse
    {
        abort_unless($user->isMember() && $user->isDeactivated(), 404);

        $user->update(['deactivated_at' => null]);

        return redirect()
            ->route('admin.enrollments.index', ['deactivated' => 1])
            ->with('status', 'Compte réactivé.');
    }
}
