<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProgramStatus;
use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\RankingSnapshot;
use App\Support\ProgramRanking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RankingController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $programs = Program::query()
            ->where('status', '!=', ProgramStatus::Draft)
            ->withCount('enrollments')
            ->with('rankingSnapshot')
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.rankings.index', [
            'programs' => $programs,
            'search' => $search,
        ]);
    }

    public function show(Program $program): View
    {
        abort_if($program->status === ProgramStatus::Draft, 404);

        $program->load('rankingSnapshot.entries');

        return view('admin.rankings.show', [
            'program' => $program,
            'rows' => ProgramRanking::for($program),
            'published' => $program->rankingSnapshot !== null,
        ]);
    }

    public function publish(Program $program): RedirectResponse
    {
        abort_if($program->status === ProgramStatus::Draft, 404);
        abort_if($program->rankingSnapshot !== null, 404);

        DB::transaction(function () use ($program): void {
            $snapshot = RankingSnapshot::query()->create([
                'program_id' => $program->id,
                'published_by' => auth()->id(),
                'published_at' => now(),
            ]);

            foreach (ProgramRanking::live($program) as $row) {
                $snapshot->entries()->create([
                    'user_id' => $row['user_id'],
                    'rank' => $row['rank'],
                    'points' => $row['points'],
                    'live_count' => $row['live_count'],
                    'username' => $row['username'],
                ]);
            }

            $program->update([
                'status' => ProgramStatus::Closed,
                'closed_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.rankings.show', $program)
            ->with('status', 'Classement publié.');
    }

    public function export(Program $program): StreamedResponse
    {
        abort_if($program->status === ProgramStatus::Draft, 404);

        $program->load('rankingSnapshot.entries');
        $rows = ProgramRanking::for($program);
        $filename = Str::slug($program->title).'-classement.csv';

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\u{FEFF}");
            fputcsv($handle, ['Rang', 'Pseudo', 'Points', 'Lives', 'Inscrit le'], ';');

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['rank'],
                    $row['username'],
                    $row['points'],
                    $row['live_count'],
                    $row['enrolled_at']?->format('d/m/Y H:i') ?? '',
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
