<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreBilanAnswersRequest;
use App\Models\Bilan;
use App\Models\BilanSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BilanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $bilans = Bilan::query()
            ->withCount('questions')
            ->with(['submissions' => fn ($query) => $query->where('user_id', $user->id)])
            ->latest()
            ->get();

        return view('member.bilans.index', [
            'bilans' => $bilans,
        ]);
    }

    public function show(Request $request, Bilan $bilan): View
    {
        $bilan->load('questions');

        $submission = $bilan->submissions()
            ->where('user_id', $request->user()->id)
            ->with('answers')
            ->first();

        return view('member.bilans.show', [
            'bilan' => $bilan,
            'submission' => $submission,
            'answers' => $submission?->valuesByQuestion() ?? [],
        ]);
    }

    public function store(StoreBilanAnswersRequest $request, Bilan $bilan): RedirectResponse
    {
        $answers = $request->input('answers', []);
        $bilan->loadMissing('questions');

        DB::transaction(function () use ($request, $bilan, $answers): void {
            $submission = BilanSubmission::query()->updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'bilan_id' => $bilan->id,
                ],
                [
                    'submitted_at' => now(),
                ],
            );

            $submission->answers()->delete();

            foreach ($bilan->questions as $question) {
                $submission->answers()->create([
                    'bilan_question_id' => $question->id,
                    'value' => (string) ($answers[$question->id] ?? ''),
                ]);
            }
        });

        return redirect()
            ->route('member.bilans.index')
            ->with('status', 'Bilan enregistré.');
    }
}
