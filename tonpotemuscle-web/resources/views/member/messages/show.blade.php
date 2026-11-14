<x-layouts.member title="Messages" :wide="true">
    <div class="h-[calc(100dvh-10.5rem)] min-h-[26rem] overflow-hidden rounded-2xl border border-border bg-surface lg:h-[calc(100dvh-8.5rem)] lg:min-h-[34rem]">
        <x-chat.thread
            :messages="$messages"
            :action="route('member.messages.store')"
            :peer-name="$coachName"
            peer-subtitle="Suivi personnalisé · réponse à sa prochaine visite"
            :refresh-url="route('member.messages.show')"
        />
    </div>
</x-layouts.member>
