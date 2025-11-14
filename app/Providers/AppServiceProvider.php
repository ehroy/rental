<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\SocialLink;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;

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
    // Share Settings
    Inertia::share('setting', fn () =>
        Setting::where('domain', request()->getHost())->first() 
        ?? Setting::first()
    );

    // Share Social Links
    Inertia::share('social_links', fn () =>
        SocialLink::orderBy('sort')->get()
    );

    // Blade Share (optional)
    View::composer('*', function ($view) {
        $view->with('setting', Setting::first());
    });

    }
}
