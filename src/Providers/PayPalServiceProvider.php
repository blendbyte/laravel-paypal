<?php

namespace Srmklive\PayPal\Providers;

/*
 * Class PayPalServiceProvider
 * @package Srmklive\PayPal
 */

use Illuminate\Support\ServiceProvider;
use Srmklive\PayPal\Commands\PublishAssetsCommand;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected bool $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish config files
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('paypal.php'),
        ]);

        // Publish Lang Files
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'paypal');

        // Register artisan commands.
        $this->registerCommands();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerPayPal();

        $this->mergeConfig();
    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    private function registerPayPal(): void
    {
        $this->app->singleton('paypal_client', static function () {
            return new PayPalClient();
        });
    }

    /**
     * Merges user's and paypal's configs.
     *
     * @return void
     */
    private function mergeConfig(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/config.php',
            'paypal'
        );
    }

    /**
     * Register the console .
     *
     * @return void
     */
    private function registerCommands(): void
    {
        $this->commands([
            PublishAssetsCommand::class,
        ]);
    }
}
