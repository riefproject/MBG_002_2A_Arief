<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        // Custom Blade directive untuk role checking
        Blade::directive('hasRole', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->role === {$role}): ?>";
        });

        Blade::directive('endhasRole', function () {
            return "<?php endif; ?>";
        });

        // Custom directive untuk multiple roles
        Blade::directive('hasAnyRole', function ($roles) {
            return "<?php if(auth()->check() && in_array(auth()->user()->role, {$roles})): ?>";
        });

        Blade::directive('endhasAnyRole', function () {
            return "<?php endif; ?>";
        });
    }
}
