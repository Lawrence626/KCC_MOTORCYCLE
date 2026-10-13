<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(app_path('Services/AppSettingService.php'))) {
            require_once app_path('Services/AppSettingService.php');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Require AppSettingService to ensure helper function app_logo_url() is globally loaded
        if (file_exists(app_path('Services/AppSettingService.php'))) {
            require_once app_path('Services/AppSettingService.php');
        }

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $view->with('appLogoUrl', \App\Services\AppSettingService::getLogoUrl());
        });
    }
}
