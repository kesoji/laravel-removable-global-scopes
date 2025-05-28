<?php

namespace Kesoji\RemovableGlobalScopes;

use Illuminate\Support\ServiceProvider;

class RemovableGlobalScopesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/removable-global-scopes.php' => config_path('removable-global-scopes.php'),
            ], 'config');
        }
    }
}