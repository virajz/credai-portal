<?php

namespace App\Providers;

use App\Models\Partner;
use App\Models\Visitor;
use App\Observers\PartnerObserver;
use App\Observers\VisitorObserver;
use Illuminate\Support\Facades\Gate;
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
        Visitor::observe(VisitorObserver::class);
        Partner::observe(PartnerObserver::class);

        Gate::define('admin-access', function ($user) {
            return $user->email === 'viraj@glam2026.com';
        });

        Gate::define('view-dashboard', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-companies', function ($user) {
            return $user->isAdmin();
        });
    }
}
