<?php

namespace StaticAssets;

use Illuminate\Foundation\Vite;
use StaticAssets\Commands\TriggerViteManifestDownload;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/static-assets.php' => config_path('static-assets.php'),
        ], 'static-assets-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                TriggerViteManifestDownload::class,
            ]);
        }

        if (config('static-assets.is_enabled')) {
            $this->app->extend(Vite::class, function () {
                return new StaticAssetVite;
            });
        }
    }
}
