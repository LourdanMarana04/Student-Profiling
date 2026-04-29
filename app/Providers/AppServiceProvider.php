<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;



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
        // Force HTTPS on production (Render)
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            $user = Auth::user();

            $view->with('authUser', $user);
            $view->with('authUserRole', $user?->role);
            $view->with('systemTheme', session('theme', 'light'));
        });
    }
}
