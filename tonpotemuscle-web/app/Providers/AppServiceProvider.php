<?php

namespace App\Providers;

use App\Support\CoachChat;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        View::composer(['components.layouts.admin', 'components.layouts.member'], function (ViewContract $view): void {
            $user = auth()->user();

            $view->with('unreadChat', $user ? CoachChat::unreadCount($user) : 0);
        });
    }
}
