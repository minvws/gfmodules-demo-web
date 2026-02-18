<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\DemoService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(DemoService::class, function ($app) {
            return new DemoService(
                storage_path('certs/client.crt'),
                storage_path('certs/client.key')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        $this->bootAuth();
    }

    public function bootAuth(): void
    {
    }
}
