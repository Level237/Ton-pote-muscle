<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterMemberRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterMemberController extends Controller
{
    public function create(): View
    {
        return view('auth.member.register');
    }

    public function store(RegisterMemberRequest $request): RedirectResponse
    {
        $user = User::query()->create($request->memberAttributes());

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
