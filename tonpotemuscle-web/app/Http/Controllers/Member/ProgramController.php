<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(Request $request): View
    {
        $enrollments = $request->user()
            ->enrollments()
            ->with('program')
            ->latest('enrolled_at')
            ->get();

        return view('member.programs.index', [
            'enrollments' => $enrollments,
        ]);
    }

    public function show(Request $request, Program $program): View
    {
        $enrollment = $request->user()
            ->enrollments()
            ->where('program_id', $program->id)
            ->firstOrFail();

        $assignments = $request->user()
            ->assignments()
            ->where('program_id', $program->id)
            ->latest('starts_on')
            ->get();

        return view('member.programs.show', [
            'program' => $program,
            'enrollment' => $enrollment,
            'assignments' => $assignments,
        ]);
    }
}
