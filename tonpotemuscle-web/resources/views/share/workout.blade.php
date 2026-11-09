<x-layouts.share :title="$shareTitle" :description="$shareText" :url="$shareUrl">
    <div class="mx-auto flex min-h-dvh w-full max-w-md flex-col justify-center px-4 py-10 sm:px-6">
        <x-share.card-workout :log="$log" />
        <div class="mt-8">
            <x-share.actions :url="$shareUrl" :title="$shareTitle" :text="$shareText" />
        </div>
        <a href="{{ route('login') }}" class="mt-6 inline-flex min-h-11 items-center justify-center text-sm font-medium text-muted hover:text-primary">
            Ton Pote Musclé
        </a>
    </div>
</x-layouts.share>
