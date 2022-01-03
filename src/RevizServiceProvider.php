<?php

namespace Antoniputra\Reviz;

use Antoniputra\Reviz\Facades\Reviz;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RevizServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mapEloquentMorph();
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
        if (! defined('REVIZ_PATH')) {
            define('REVIZ_PATH', realpath(__DIR__.'/../'));
        }

        $this->mergeConfigFrom(
            __DIR__.'/../config/reviz.php', 'reviz'
        );

        $this->app->singleton(RevizManager::class, function () {
            return new RevizManager;
        });
    }

    protected function mapEloquentMorph()
    {
        if ($morphMaps = $this->app['config']->get('reviz.morphMap')) {
            Relation::morphMap($morphMaps);
        }
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
            REVIZ_PATH.'/public' => public_path('vendor/reviz'),
        ], 'reviz-assets');

        $this->loadViewsFrom(
            __DIR__.'/../resources/views', 'reviz'
        );
    }

    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            Route::get('/', 'AdminController@index')->name('revizPanel.index');
            Route::get('/{id}/show', 'AdminController@show')->name('revizPanel.show');
        });
    }

    private function routeConfiguration()
    {
        return [
            'namespace' => 'Antoniputra\Reviz\Http\Controllers',
            'prefix' => config('reviz.ui.prefixPath'),
            'middleware' => config('reviz.ui.middleware', 'web'),
        ];
    }
}
