<?php

namespace App\Support;

use App\Models\CoachMessage;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class CoachChat
{
    public static function unreadCount(User $user): int
    {
        $query = CoachMessage::query()->whereNull('read_at');

        if ($user->isAdmin()) {
            return $query->where('from_member', true)->count();
        }

        return $query
            ->where('member_id', $user->id)
            ->where('from_member', false)
            ->count();
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public static function inbox(?string $search = null, bool $unreadOnly = false): LengthAwarePaginator
    {
        $search = trim((string) $search);

        return User::query()
            ->members()
            ->active()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->when($unreadOnly, function (Builder $query): void {
                $query->whereExists(function ($query): void {
                    $query->selectRaw('1')
                        ->from('coach_messages')
                        ->whereColumn('coach_messages.member_id', 'users.id')
                        ->whereNull('read_at')
                        ->where('from_member', true);
                });
            })
            ->with('latestCoachMessage')
            ->withCount([
                'coachMessages as unread_count' => function (Builder $query): void {
                    $query->whereNull('read_at')->where('from_member', true);
                },
            ])
            ->orderByRaw('(select max(id) from coach_messages where member_id = users.id) is null')
            ->orderByDesc(
                CoachMessage::query()
                    ->select('id')
                    ->whereColumn('member_id', 'users.id')
                    ->latest('id')
                    ->limit(1),
            )
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * @return Collection<int, CoachMessage>
     */
    public static function thread(User $member, int $limit = 100): Collection
    {
        return CoachMessage::query()
            ->where('member_id', $member->id)
            ->with('author')
            ->latest('id')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    public static function markRead(User $member, User $reader): void
    {
        CoachMessage::query()
            ->where('member_id', $member->id)
            ->whereNull('read_at')
            ->where('from_member', $reader->isAdmin())
            ->update(['read_at' => now()]);
    }

    public static function send(User $member, User $author, string $body): CoachMessage
    {
        return CoachMessage::query()->create([
            'member_id' => $member->id,
            'author_id' => $author->id,
            'body' => $body,
            'from_member' => $author->isMember(),
        ]);
    }
}
