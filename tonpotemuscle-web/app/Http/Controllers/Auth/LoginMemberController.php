<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginMemberRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginMemberController extends Controller
{
    public function create(): View
    {
        return view('auth.member.login');
    }

    public function store(LoginMemberRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
