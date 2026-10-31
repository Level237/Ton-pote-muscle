<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginAdminRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginAdminController extends Controller
{
    public function create(): View
    {
        return view('auth.admin.login');
    }

    public function store(LoginAdminRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }
}
