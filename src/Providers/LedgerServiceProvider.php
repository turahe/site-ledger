<?php

namespace Turahe\Ledger\Providers;

use Illuminate\Support\ServiceProvider;
use Turahe\Ledger\Services\LedgerService;

class LedgerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerConfig();
        $this->registerServices();
    }

    /**
     * Boot the application events.
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
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'ledger');
    }

    /**
     * Register services.
     */
    protected function registerServices(): void
    {
        $this->app->singleton(LedgerService::class, function ($app) {
            return new LedgerService;
        });

        $this->app->alias(LedgerService::class, 'ledger.service');
    }
}
