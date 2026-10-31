<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkoutRequest;
use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WorkoutController extends Controller
{
    public function index(Request $request): View
    {
        $archived = $request->boolean('archived');
        $search = $request->string('q')->trim()->toString();

        $workouts = Workout::query()
            ->withCount('items')
            ->when($archived, fn ($query) => $query->onlyTrashed())
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.workouts.index', [
            'workouts' => $workouts,
            'archived' => $archived,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.workouts.create', $this->formData(new Workout));
    }

    public function store(WorkoutRequest $request): RedirectResponse
    {
        $workout = DB::transaction(function () use ($request) {
            $workout = Workout::query()->create($this->payload($request));
            $this->syncMedia($request, $workout);
            $this->syncItems($workout, $request->input('items', []));

            return $workout;
        });

        return redirect()
            ->route('admin.workouts.index')
            ->with('status', 'Séance créée.');
    }

    public function edit(Workout $workout): View
    {
        $workout->load('items');

        return view('admin.workouts.edit', $this->formData($workout));
    }

    public function update(WorkoutRequest $request, Workout $workout): RedirectResponse
    {
        DB::transaction(function () use ($request, $workout) {
            $workout->update($this->payload($request));
            $this->syncMedia($request, $workout);
            $this->syncItems($workout, $request->input('items', []));
        });

        return redirect()
            ->route('admin.workouts.edit', $workout)
            ->with('status', 'Séance enregistrée.');
    }

    public function destroy(Workout $workout): RedirectResponse
    {
        $workout->delete();

        return redirect()
            ->route('admin.workouts.index')
            ->with('status', 'Séance archivée.');
    }

    public function restore(string $workout): RedirectResponse
    {
        Workout::onlyTrashed()->findOrFail($workout)->restore();

        return redirect()
            ->route('admin.workouts.index')
            ->with('status', 'Séance restaurée.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Workout $workout): array
    {
        $items = old('items');

        if (! is_array($items)) {
            $items = $workout->items->map(fn ($item) => [
                'exercise_id' => $item->exercise_id,
                'reps' => $item->reps,
                'duration_seconds' => $item->duration_seconds,
                'rest_seconds' => $item->rest_seconds,
            ])->all();
        }

        if ($items === []) {
            $items = [[
                'exercise_id' => '',
                'reps' => '',
                'duration_seconds' => '',
                'rest_seconds' => 30,
            ]];
        }

        return [
            'workout' => $workout,
            'exercises' => Exercise::query()->orderBy('title')->get(['id', 'title']),
            'items' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(WorkoutRequest $request): array
    {
        return [
            'title' => $request->string('title')->toString(),
            'difficulty' => $request->input('difficulty') ?: null,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    private function syncItems(Workout $workout, array $items): void
    {
        $workout->items()->delete();

        $total = 0;
        $position = 1;

        foreach ($items as $item) {
            $duration = filled($item['duration_seconds'] ?? null) ? (int) $item['duration_seconds'] : null;
            $reps = filled($item['reps'] ?? null) ? (int) $item['reps'] : null;
            $rest = (int) ($item['rest_seconds'] ?? 30);

            $workout->items()->create([
                'exercise_id' => $item['exercise_id'],
                'position' => $position++,
                'reps' => $reps,
                'duration_seconds' => $duration,
                'rest_seconds' => $rest,
            ]);

            $total += ($duration ?? 0) + $rest;
        }

        $workout->update(['total_duration_seconds' => $total]);
    }

    private function syncMedia(WorkoutRequest $request, Workout $workout): void
    {
        $updates = [];

        if ($request->boolean('remove_thumbnail') && $workout->thumbnail_path) {
            Storage::disk('public')->delete($workout->thumbnail_path);
            $updates['thumbnail_path'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            if ($workout->thumbnail_path) {
                Storage::disk('public')->delete($workout->thumbnail_path);
            }

            $updates['thumbnail_path'] = $request->file('thumbnail')->store('workouts', 'public');
        }

        if ($updates !== []) {
            $workout->update($updates);
        }
    }
}
