<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Support\MemberTraining;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $member = $request->user();

        return view('member.dashboard', array_merge(
            MemberTraining::board($member, $request),
            ['member' => $member],
        ));
    }
}
