<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProgramStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProgramRequest;
use App\Models\Program;
use App\Models\Workout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(Request $request): View
    {
        $archived = $request->boolean('archived');
        $search = $request->string('q')->trim()->toString();

        $programs = Program::query()
            ->withCount('schedule')
            ->when($archived, fn ($query) => $query->onlyTrashed())
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.programs.index', [
            'programs' => $programs,
            'archived' => $archived,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.programs.create', $this->formData(new Program([
            'status' => ProgramStatus::Draft,
            'starts_on' => now()->toDateString(),
            'ends_on' => now()->addWeeks(4)->toDateString(),
        ])));
    }

    public function store(ProgramRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $program = Program::query()->create($this->payload($request));
            $this->syncMedia($request, $program);
            $this->syncSchedule($program, $request->input('slots', []));
        });

        return redirect()
            ->route('admin.programs.index')
            ->with('status', 'Programme créé.');
    }

    public function edit(Program $program): View
    {
        $program->load('schedule');

        return view('admin.programs.edit', $this->formData($program));
    }

    public function update(ProgramRequest $request, Program $program): RedirectResponse
    {
        DB::transaction(function () use ($request, $program) {
            $program->update($this->payload($request));
            $this->syncMedia($request, $program);
            $this->syncSchedule($program, $request->input('slots', []));
        });

        return redirect()
            ->route('admin.programs.edit', $program)
            ->with('status', 'Programme enregistré.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        $program->delete();

        return redirect()
            ->route('admin.programs.index')
            ->with('status', 'Programme archivé.');
    }

    public function restore(string $program): RedirectResponse
    {
        Program::onlyTrashed()->findOrFail($program)->restore();

        return redirect()
            ->route('admin.programs.index')
            ->with('status', 'Programme restauré.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Program $program): array
    {
        $slots = old('slots');

        if (! is_array($slots)) {
            $slots = $program->schedule->map(fn ($slot) => [
                'day_number' => $slot->day_number,
                'workout_id' => $slot->workout_id,
            ])->all();
        }

        if ($slots === []) {
            $slots = [[
                'day_number' => 1,
                'workout_id' => '',
            ]];
        }

        return [
            'program' => $program,
            'workouts' => Workout::query()->orderBy('title')->get(['id', 'title']),
            'slots' => $slots,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(ProgramRequest $request): array
    {
        $status = ProgramStatus::from($request->string('status')->toString());

        return [
            'title' => $request->string('title')->toString(),
            'description' => $request->input('description') ?: null,
            'objective' => $request->input('objective') ?: null,
            'level' => $request->input('level') ?: null,
            'starts_on' => $request->date('starts_on'),
            'ends_on' => $request->date('ends_on'),
            'status' => $status,
            'closed_at' => $status === ProgramStatus::Closed ? ($request->route('program')?->closed_at ?? now()) : null,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $slots
     */
    private function syncSchedule(Program $program, array $slots): void
    {
        $program->schedule()->delete();

        foreach ($slots as $slot) {
            $program->schedule()->create([
                'day_number' => (int) $slot['day_number'],
                'workout_id' => $slot['workout_id'],
            ]);
        }
    }

    private function syncMedia(ProgramRequest $request, Program $program): void
    {
        $updates = [];

        if ($request->boolean('remove_image') && $program->image_path) {
            Storage::disk('public')->delete($program->image_path);
            $updates['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            if ($program->image_path) {
                Storage::disk('public')->delete($program->image_path);
            }

            $updates['image_path'] = $request->file('image')->store('programs', 'public');
        }

        if ($updates !== []) {
            $program->update($updates);
        }
    }
}
