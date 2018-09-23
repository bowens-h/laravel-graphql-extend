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
        if ($this->app->runningInConsole()) {
            $this->commands([
                ColumnMakeCommand::class,
                TypeMakeCommand::class
            ]);
        }
    }

    /**
     * Register any application services.
     */
    public function register()
    {

    }
}
