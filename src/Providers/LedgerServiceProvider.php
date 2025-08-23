<?php

namespace Turahe\Ledger\Providers;

use Illuminate\Support\ServiceProvider;
use Turahe\Ledger\Services\LedgerService;

/**
 * Ledger Service Provider
 *
 * Main service provider for the Turahe Ledger package.
 * Handles package registration, configuration publishing, and service binding.
 *
 * Features:
 * - Automatic migration loading
 * - Configuration file publishing
 * - Service registration and binding
 * - Console command registration
 *
 * @package Turahe\Ledger\Providers
 * @author  Nur Wachid <wachid@outlook.com>
 * @since   1.0.0
 */
class LedgerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Called during the service container binding phase.
     * Registers configuration and core services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerConfig();
        $this->registerServices();
    }

    /**
     * Boot the application events.
     *
     * Called after all services are registered.
     * Loads migrations and publishes configuration files.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/config.php' => config_path('ledger.php'),
            ], 'config');
        }
    }

    /**
     * Register configuration files.
     *
     * Merges the package configuration with the application's config.
     * Allows users to override default settings.
     *
     * @return void
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'ledger');
    }

    /**
     * Register core services.
     *
     * Binds the LedgerService as a singleton and creates an alias
     * for easy access throughout the application.
     *
     * @return void
     */
    protected function registerServices(): void
    {
        $this->app->singleton(LedgerService::class, function ($app) {
            return new LedgerService;
        });

        $this->app->alias(LedgerService::class, 'ledger.service');
    }
}
