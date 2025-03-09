<?php

namespace Shahadh\TemporaryLinks;

use Illuminate\Support\ServiceProvider;
use Shahadh\TemporaryLinks\Services\TemporaryLinkService;

class TemporaryLinksServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'temporary-links-migrations');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/temporary-links.php' => config_path('temporary-links.php'),
        ], 'temporary-links-config');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }

    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/../config/temporary-links.php', 'temporary-links');

        // Register service
        $this->app->singleton('temporary-link', function ($app) {
            return new TemporaryLinkService();
        });

        $this->app->singleton(WebhookService::class, function ($app) {
            return new WebhookService();
        });

        // Register event service provider
        $this->app->register(TemporaryLinksEventServiceProvider::class);
    }

    protected function getMigrationFileName($migrationFileName)
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make('files');

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $migrationFileName) {
                return $filesystem->glob($path.'*_'.$migrationFileName);
            })
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}