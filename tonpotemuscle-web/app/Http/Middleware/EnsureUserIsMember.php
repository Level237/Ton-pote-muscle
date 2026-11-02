<?php

namespace App\Http\Middleware;

use App\Models\Bilan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsMember
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isMember()) {
            abort(403);
        }

        if ($user->isDeactivated()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Ce compte a été désactivé.']);
        }

        $departure = Bilan::departure();

        if ($departure && ! $user->bilanSubmissions()->where('bilan_id', $departure->id)->exists()) {
            $onboarding = $request->routeIs('member.bilans.show', 'member.bilans.store')
                && $request->route('bilan')?->is($departure);

            if (! $onboarding && ! $request->routeIs('logout')) {
                return redirect()->route('member.bilans.show', $departure);
            }
        }

        return $next($request);
    }
}
