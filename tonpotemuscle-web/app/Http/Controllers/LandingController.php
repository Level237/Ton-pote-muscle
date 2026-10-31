<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): RedirectResponse|View
    {
        if (auth()->check()) {
            $user = auth()->user();

            return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'dashboard');
        }

        return view('landing');
    }
}
