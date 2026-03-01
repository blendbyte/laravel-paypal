<?php

namespace Srmklive\PayPal\Providers;

use Illuminate\Support\ServiceProvider;
use Srmklive\PayPal\Services\PayTheFly as PayTheFlyClient;

class PayTheFlyServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../../config/paythefly.php' => config_path('paythefly.php'),
        ], 'paythefly-config');

        // Publish migration
        if (file_exists(__DIR__ . '/../../database/migrations')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/' => database_path('migrations'),
            ], 'paythefly-migrations');
        }

        // Load routes for webhook
        $this->loadRoutesFrom(__DIR__ . '/../../routes/paythefly.php');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/paythefly.php',
            'paythefly'
        );

        $this->app->singleton('paythefly_client', static function ($app) {
            return new PayTheFlyClient($app['config']->get('paythefly', []));
        });

        $this->app->alias('paythefly_client', PayTheFlyClient::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['paythefly_client', PayTheFlyClient::class];
    }
}
