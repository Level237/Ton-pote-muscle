<x-layouts.admin :title="'Classement · '.$program->title">
    <div class="space-y-6">
        <header class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('admin.rankings.index') }}" class="text-sm text-muted hover:text-primary">Classements</a>
                <h1 class="mt-1 font-display text-2xl font-bold uppercase tracking-wide">{{ $program->title }}</h1>
                <p class="mt-1 text-sm tabular-nums text-muted">
                    {{ $program->starts_on->format('d/m/Y') }} au {{ $program->ends_on->format('d/m/Y') }}
                    · {{ $published ? 'Publié' : 'Provisoire' }}
                    · {{ $program->status->label() }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('admin.rankings.export', $program) }}"
                    class="inline-flex min-h-11 items-center rounded-lg border border-border bg-surface px-4 text-sm font-medium text-text hover:border-primary hover:text-primary"
                >
                    Export CSV
                </a>

                @unless ($published)
                    <form method="POST" action="{{ route('admin.rankings.publish', $program) }}">
                        @csrf
                        <button type="submit" class="inline-flex min-h-11 items-center rounded-lg bg-primary px-4 text-sm font-semibold text-bg hover:bg-primary-hover">
                            Clôturer et publier
                        </button>
                    </form>
                @endunless
            </div>
        </header>

        <section class="overflow-hidden rounded-xl border border-border bg-surface">
            @if ($rows->isEmpty())
                <p class="px-5 py-10 text-sm text-muted">Aucun inscrit sur ce programme.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-border text-xs uppercase tracking-wide text-muted">
                            <tr>
                                <th class="px-4 py-3 font-medium">Rang</th>
                                <th class="px-4 py-3 font-medium">Pseudo</th>
                                <th class="px-4 py-3 font-medium tabular-nums">Points</th>
                                <th class="hidden px-4 py-3 font-medium tabular-nums sm:table-cell">Lives</th>
                                @unless ($published)
                                    <th class="hidden px-4 py-3 font-medium sm:table-cell">Inscrit le</th>
                                @endunless
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($rows as $row)
                                <tr class="{{ $row['rank'] === 1 ? 'bg-primary/5' : '' }}">
                                    <td class="px-4 py-3.5 font-display text-base font-bold tabular-nums {{ $row['rank'] === 1 ? 'text-primary' : 'text-text' }}">
                                        {{ $row['rank'] }}
                                    </td>
                                    <td class="px-4 py-3.5 font-medium text-text">{{ $row['username'] }}</td>
                                    <td class="px-4 py-3.5 tabular-nums text-text">{{ $row['points'] }}</td>
                                    <td class="hidden px-4 py-3.5 tabular-nums text-muted sm:table-cell">{{ $row['live_count'] }}</td>
                                    @unless ($published)
                                        <td class="hidden px-4 py-3.5 tabular-nums text-muted sm:table-cell">
                                            {{ $row['enrolled_at']?->format('d/m/Y') }}
                                        </td>
                                    @endunless
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-layouts.admin>
