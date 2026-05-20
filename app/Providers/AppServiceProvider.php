<?php

declare(strict_types=1);

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('gfmodules.prs_oauth_client', function () {
            return new Client([
                'base_uri' => config('gfmodules.prs.oauth_url'),
                'cert' => config('gfmodules.client_cert'),
                'ssl_key' => config('gfmodules.client_key'),
                'verify' => config('gfmodules.client_verify', true),
            ]);
        });

        $this->app->singleton('gfmodules.nvi_oauth_client', function () {
            return new Client([
                'base_uri' => config('gfmodules.nvi.oauth_url'),
                'cert' => config('gfmodules.client_cert'),
                'ssl_key' => config('gfmodules.client_key'),
                'verify' => config('gfmodules.client_verify', true),
            ]);
        });

        $this->app->singleton('gfmodules.prs_client', function () {
            return new Client([
                'base_uri' => config('gfmodules.prs.url'),
                'cert' => config('gfmodules.client_cert'),
                'ssl_key' => config('gfmodules.client_key'),
                'verify' => config('gfmodules.client_verify', true),
            ]);
        });

        $this->app->singleton('gfmodules.nvi_client', function () {
            return new Client([
                'base_uri' => config('gfmodules.nvi.url'),
                'cert' => config('gfmodules.client_cert'),
                'ssl_key' => config('gfmodules.client_key'),
                'verify' => config('gfmodules.client_verify', true),
            ]);
        });
    }

    /**
     * Bootstrap any application services.
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
