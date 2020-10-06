<?php

namespace LaravelSubscription;

use Illuminate\Support\ServiceProvider;

class LaravelSubscriptionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerResources();
        $this->registerMigrations();
        $this->registerPublishing();

        // Load WayForPay and other payment providers
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();
    }

    /**
     * Setup the configuration for Cashier.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/subscription.php', 'subscription'
        );
    }

    /**
     * Register the package resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'subscription');
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (LaravelSubscription::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/subscription.php' => $this->app->configPath('subscription.php'),
            ], 'cashier-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ], 'subscription-migrations');

            $this->publishes([
                __DIR__.'/../resources/views' => $this->app->resourcePath('views/vendor/subscription'),
            ], 'subscription-views');
        }
    }
}
