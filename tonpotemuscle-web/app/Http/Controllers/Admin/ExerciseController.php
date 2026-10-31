<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExerciseRequest;
use App\Models\Exercise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    public function index(Request $request): View
    {
        $archived = $request->boolean('archived');
        $search = $request->string('q')->trim()->toString();

        $exercises = Exercise::query()
            ->when($archived, fn ($query) => $query->onlyTrashed())
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.exercises.index', [
            'exercises' => $exercises,
            'archived' => $archived,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.exercises.create', [
            'exercise' => new Exercise,
        ]);
    }

    public function store(ExerciseRequest $request): RedirectResponse
    {
        $exercise = Exercise::query()->create($this->payload($request));
        $this->syncMedia($request, $exercise);

        return redirect()
            ->route('admin.exercises.index')
            ->with('status', 'Exercice créé.');
    }

    public function edit(Exercise $exercise): View
    {
        return view('admin.exercises.edit', [
            'exercise' => $exercise,
        ]);
    }

    public function update(ExerciseRequest $request, Exercise $exercise): RedirectResponse
    {
        $exercise->update($this->payload($request));
        $this->syncMedia($request, $exercise);

        return redirect()
            ->route('admin.exercises.edit', $exercise)
            ->with('status', 'Exercice enregistré.');
    }

    public function destroy(Exercise $exercise): RedirectResponse
    {
        $exercise->delete();

        return redirect()
            ->route('admin.exercises.index')
            ->with('status', 'Exercice archivé.');
    }

    public function restore(string $exercise): RedirectResponse
    {
        Exercise::onlyTrashed()->findOrFail($exercise)->restore();

        return redirect()
            ->route('admin.exercises.index')
            ->with('status', 'Exercice restauré.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(ExerciseRequest $request): array
    {
        return [
            'title' => $request->string('title')->toString(),
            'description' => $request->input('description') ?: null,
            'video_url' => $request->input('video_url') ?: null,
            'body_parts' => $request->input('body_parts', []),
            'equipments' => $request->input('equipments', []),
            'target_muscles' => $this->splitList($request->input('target_muscles')),
            'secondary_muscles' => $this->splitList($request->input('secondary_muscles')),
            'instructions' => $this->splitLines($request->input('instructions')),
        ];
    }

    private function syncMedia(ExerciseRequest $request, Exercise $exercise): void
    {
        $updates = [];

        if ($request->boolean('remove_image') && $exercise->image_path) {
            $this->deleteFile($exercise->image_path);
            $updates['image_path'] = null;
        }

        if ($request->boolean('remove_gif') && $exercise->gif_path) {
            $this->deleteFile($exercise->gif_path);
            $updates['gif_path'] = null;
        }

        if ($request->hasFile('image')) {
            $this->deleteFile($exercise->image_path);
            $updates['image_path'] = $request->file('image')->store('exercises', 'public');
        }

        if ($request->hasFile('gif')) {
            $this->deleteFile($exercise->gif_path);
            $updates['gif_path'] = $request->file('gif')->store('exercises/gifs', 'public');
        }

        if ($updates !== []) {
            $exercise->update($updates);
        }
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @return list<string>
     */
    private function splitList(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }

    /**
     * @return list<string>
     */
    private function splitLines(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $value) ?: [])));
    }
}
