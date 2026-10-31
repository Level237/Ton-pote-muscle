<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function __invoke(Program $program): View
    {
        abort_unless($program->isPublic(), 404);

        $program->load(['schedule.workout']);

        return view('programs.show', [
            'program' => $program,
        ]);
    }
}
