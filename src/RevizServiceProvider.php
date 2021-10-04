<?php

namespace Antoniputra\Reviz;

use Antoniputra\Reviz\Facades\Reviz;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class RevizServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerResources();
        Event::listen([RequestHandled::class, RevizStoreEvent::class], function ($event) {
            $this->handleListeners($event);
        });
    }

    public function register()
    {
        $this->app->singleton(RevizManager::class, function () {
            return new RevizManager;
        });
    }

    protected function registerResources()
    {
        $this->publishes([
            __DIR__.'/../config/reviz.php' => config_path('reviz.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../config/reviz.php', 'reviz'
        );

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function handleListeners($event)
    {
        if (Reviz::getItems()->isNotEmpty()) {
            Reviz::store($event);
        }
    }
}
