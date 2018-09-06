<?php

namespace Yxz\LaravelTools;

use Illuminate\Support\ServiceProvider;
use Yxz\LaravelTools\Console\CommandFile;
use Yxz\LaravelTools\Console\Provider;
use Yxz\LaravelTools\Support\LogSql;

class LaravelToolsServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        \DB::listen(function ($sql) {
            $this->app->make('laravel-tools.log-sql')->printSql($sql);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registLogSql();

        $this->commands(
            CommandFile::class,
            Provider::class
        );
    }

    private function registLogSql()
    {
        $configPath = __DIR__ . '/config/db-sql.php';
        $this->mergeConfigFrom($configPath, 'database.db-sql');
//print_r($this->app['config']);
        $this->app->singleton(
            'laravel-tools.log-sql',
            function ($app) {
                return new LogSql($app['config']['database.db-sql']);
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [CommandFile::class, Provider::class];
    }
}
