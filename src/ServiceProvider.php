<?php

namespace BowensH\LaravelGraphQLExtend;

use BowensH\LaravelGraphQLExtend\Console\Commands\ColumnMakeCommand;
use BowensH\LaravelGraphQLExtend\Console\Commands\TypeMakeCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->bootCommands();

        $this->bootPublishes();
    }

    protected function bootCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ColumnMakeCommand::class,
                TypeMakeCommand::class
            ]);
        }
    }

    /**
     * Bootstrap publishes
     *
     * @return void
     */
    protected function bootPublishes()
    {
        $configPath = __DIR__.'/config';

        $this->mergeConfigFrom($configPath.'/config.php', 'graphql_extend');

        $this->publishes([
            $configPath.'/config.php' => config_path('graphql_extend.php'),
        ], 'config');
    }
    /**
     * Register any application services.
     */
    public function register()
    {

    }
}
