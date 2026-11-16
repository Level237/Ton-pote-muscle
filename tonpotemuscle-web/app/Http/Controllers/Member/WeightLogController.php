<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreWeightLogRequest;
use App\Support\MemberWeight;
use Illuminate\Http\RedirectResponse;

class WeightLogController extends Controller
{
    public function store(StoreWeightLogRequest $request): RedirectResponse
    {
        MemberWeight::record($request->user(), $request->input('weight_kg'));

        return redirect()
            ->to(route('dashboard', ['suivi' => 'pese']).'#suivi')
            ->with('status', 'Pesée enregistrée.');
    }
}
