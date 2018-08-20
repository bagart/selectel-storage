<?php

namespace BAGArt\SelectelStorage;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class SelectelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind(
            SelectelOpenStack::class,
            function (Application $app, array $config) {
                return new SelectelOpenStack(
                    $config['endpoint'],
                    $config
                );
            }
        );

        $this->app->bind(
            SelectelAdapter::class,
            function (Application $app, array $config) {
                return new SelectelAdapter(
                    $this->app->make(SelectelOpenStack::class, $config)
                        ->buildContainer()
                );
            }
        );

        $this->app->make('filesystem')
            ->extend(
                'selectel',
                function (Application $app, $config) {
                    return new Filesystem(
                        $this->app->make(SelectelAdapter::class, $config)
                    );
                }
            );
    }

    public function register()
    {
        //
    }
}
