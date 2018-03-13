<?php

namespace Ltsochev\Auth;

use RuntimeException;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $config = $this->app['config']['auth'];

        if (! array_key_exists('driver_name', $config)) {
            throw new RuntimeException("Missing 'driver_name' in the 'auth' configuration file.");
        }

        $this->app['auth']->extend($config['driver_name'], function ($app) use ($config) {
            return new EloquentUserProvider($app['db']->connection(), $app['hash'], $app['request'], $config);
        });
    }
}
