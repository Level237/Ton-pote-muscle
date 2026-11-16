<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreHydrationLogRequest;
use App\Support\MemberHydration;
use Illuminate\Http\RedirectResponse;

class HydrationLogController extends Controller
{
    public function store(StoreHydrationLogRequest $request): RedirectResponse
    {
        MemberHydration::sip($request->user(), (int) $request->validated('delta'));

        return redirect()->to(route('dashboard', ['suivi' => 'eau']).'#suivi');
    }
}
