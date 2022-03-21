<?php

namespace Go2Flow\SaasRegisterLogin;

use Go2Flow\SaasRegisterLogin\Http\Middleware\TeamsPermission;
use Go2Flow\SaasRegisterLogin\Repositories\PermissionRepository;
use Go2Flow\SaasRegisterLogin\Repositories\PermissionRepositoryInterface;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class SaasRegisterLoginServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(Router $router)
    {
        $router->middlewareGroup('TeamPermission', [TeamsPermission::class]);

        /*
         * Optional methods to load your package assets
         */
        $this->app->bind(
            PermissionRepository::class,
            PermissionRepositoryInterface::class
        );

        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'saas-register-login');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'saas-register-login');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');



        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('saas-register-login.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/pspserver'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/pspserver'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/pspserver'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'saas-register-login');

        // Register the main class to use with the facade
        $this->app->singleton('saas-register-login', function () {
            return new SaasRegisterLogin;
        });


    }
}
