<?php

namespace Antoniputra\Reviz;

use Antoniputra\Reviz\Facades\Reviz;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RevizServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerResources();
        $this->registerEvents();
        
        /**
         * UI
         */
        if (config('reviz.ui.enabled')) {
            $this->registerUi();
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/reviz.php', 'reviz'
        );

        $this->app->singleton(RevizManager::class, function () {
            return new RevizManager;
        });
    }

    protected function registerResources()
    {
        $this->publishes([
            __DIR__.'/../config/reviz.php' => config_path('reviz.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    private function registerEvents()
    {
        $this->app['events']->listen([RequestHandled::class, RevizStoreEvent::class], function ($event) {
            if (Reviz::getItems()->isNotEmpty()) {
                Reviz::store($event);
            }
        });
    }

    private function registerUI()
    {
        $this->registerRoutes();

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/reviz'),
        ], 'reviz-assets');

        $this->loadViewsFrom(
            __DIR__.'/../resources/views', 'reviz'
        );
    }

    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            Route::get('/', 'AdminController@index');
        });
    }

    private function routeConfiguration()
    {
        return [
            // 'domain' => config('reviz.domain', null),
            'namespace' => 'Antoniputra\Reviz\Http\Controllers',
            'prefix' => config('reviz.ui.path'),
            // 'middleware' => 'reviz',
        ];
    }
}
