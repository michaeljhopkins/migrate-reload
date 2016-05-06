<?php

namespace Hopkins\MigrateReload;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class MigrateReloadServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['command.migrate.reload'] = $this->app->share(
            function ($app) {
                return new MigrateReloadCommand($app);
            }
        );
        $this->commands('command.migrate.reload');
    }
   
    public function provides()
    {
        return array('command.migrate.reload');
    }
}