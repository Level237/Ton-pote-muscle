<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BilanQuestionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BilanRequest;
use App\Models\Bilan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BilanController extends Controller
{
    public function index(Request $request): View
    {
        $archived = $request->boolean('archived');
        $search = $request->string('q')->trim()->toString();

        $bilans = Bilan::query()
            ->withCount('questions')
            ->when($archived, fn ($query) => $query->onlyTrashed())
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.bilans.index', [
            'bilans' => $bilans,
            'archived' => $archived,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.bilans.create', $this->formData(new Bilan));
    }

    public function store(BilanRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $bilan = Bilan::query()->create($this->payload($request));
            $this->syncQuestions($bilan, $request->input('questions', []));
        });

        return redirect()
            ->route('admin.bilans.index')
            ->with('status', 'Bilan créé.');
    }

    public function edit(Bilan $bilan): View
    {
        $bilan->load('questions');

        return view('admin.bilans.edit', $this->formData($bilan));
    }

    public function update(BilanRequest $request, Bilan $bilan): RedirectResponse
    {
        DB::transaction(function () use ($request, $bilan): void {
            $bilan->update($this->payload($request));
            $this->syncQuestions($bilan, $request->input('questions', []));
        });

        return redirect()
            ->route('admin.bilans.edit', $bilan)
            ->with('status', 'Bilan enregistré.');
    }

    public function destroy(Bilan $bilan): RedirectResponse
    {
        $bilan->delete();

        return redirect()
            ->route('admin.bilans.index')
            ->with('status', 'Bilan archivé.');
    }

    public function restore(string $bilan): RedirectResponse
    {
        Bilan::onlyTrashed()->findOrFail($bilan)->restore();

        return redirect()
            ->route('admin.bilans.index')
            ->with('status', 'Bilan restauré.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Bilan $bilan): array
    {
        $questions = old('questions');

        if (! is_array($questions)) {
            $questions = $bilan->questions->map(fn ($question) => [
                'title' => $question->title,
                'type' => $question->type->value,
                'options' => implode("\n", $question->options ?? []),
            ])->all();
        }

        if ($questions === []) {
            $questions = [[
                'title' => '',
                'type' => BilanQuestionType::Text->value,
                'options' => '',
            ]];
        }

        return [
            'bilan' => $bilan,
            'questions' => $questions,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(BilanRequest $request): array
    {
        return [
            'title' => $request->string('title')->toString(),
            'description' => $request->input('description') ?: null,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $questions
     */
    private function syncQuestions(Bilan $bilan, array $questions): void
    {
        $bilan->questions()->delete();

        $position = 1;

        foreach ($questions as $question) {
            $bilan->questions()->create([
                'position' => $position++,
                'title' => $question['title'],
                'type' => $question['type'],
                'options' => $question['options'] ?: null,
            ]);
        }
    }
}
