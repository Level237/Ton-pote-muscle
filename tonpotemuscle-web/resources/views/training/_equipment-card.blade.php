@php
    use App\Support\MemberEquipment;
@endphp

@if ($coach)
    <section class="rounded-xl border border-border bg-surface p-5">
        <p class="text-[11px] font-bold uppercase tracking-wider text-primary">Matériel</p>
        <p class="mt-2 font-display text-3xl font-bold tracking-wide">{{ MemberEquipment::statusLabel($member) }}</p>
        @if ($member->equipment_notes)
            <p class="mt-1 text-sm text-muted">{{ $member->equipment_notes }}</p>
        @elseif (MemberEquipment::declined($member))
            <p class="mt-1 text-sm text-muted">Séances au poids du corps.</p>
        @else
            <p class="mt-1 text-sm text-muted">Renseigné dans le bilan de départ.</p>
        @endif
    </section>
@endif
