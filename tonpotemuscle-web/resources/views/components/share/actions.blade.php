@props([
    'url',
    'title',
    'text',
    'primary' => 'Partager',
])

<div
    id="share-sheet"
    class="space-y-3"
    data-share-url="{{ $url }}"
    data-share-title="{{ $title }}"
    data-share-text="{{ $text }}"
>
    <button
        type="button"
        data-share-native
        class="flex min-h-12 w-full items-center justify-center rounded-xl bg-primary px-4 font-display text-sm font-bold uppercase tracking-wider text-bg transition-transform duration-150 ease-out hover:bg-primary-hover active:scale-[0.97] motion-reduce:transform-none"
    >
        {{ $primary }}
    </button>
    <button
        type="button"
        data-share-copy
        class="flex min-h-12 w-full items-center justify-center rounded-xl border border-border px-4 text-sm font-medium text-text transition-transform duration-150 ease-out hover:border-primary hover:text-primary active:scale-[0.97] motion-reduce:transform-none"
    >
        Copier le lien
    </button>
    <p data-share-status class="hidden text-center text-sm text-primary" role="status"></p>
</div>
