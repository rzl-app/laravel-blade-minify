<?php

namespace RzlApp\BladeMinify\Providers;

use Illuminate\Support\ServiceProvider;
use RzlApp\BladeMinify\RzlBladeMinifyFacade;

class RzlBladeMinifyServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap the application services.
   */
  public function boot(): void
  {
    if ($this->app->runningInConsole()) {
      $this->publishes([
        __DIR__ . '/../config/config.php' => config_path('rzl-blade-minify.php'),
      ], 'RzlLaravelHtmlMinify');
    }
  }

  /**
   * Register the application services.
   */
  public function register(): void
  {
    // Automatically apply the package configuration
    $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'rzl-blade-minify');

    // Register the main class to use with the facade
    $this->app->singleton('rzl-blade-minify', function () {
      return new RzlBladeMinifyFacade;
    });

    $this->app['router']->middleware('RzlLaravelHtmlMinify', 'RzlApp\BladeMinify\Middleware\RzlBladeMinify');

    $this->app['router']->aliasMiddleware('RzlLaravelHtmlMinify', \RzlApp\BladeMinify\Middleware\RzlBladeMinify::class);
    $this->app['router']->pushMiddlewareToGroup('web', \RzlApp\BladeMinify\Middleware\RzlBladeMinify::class);
  }
}
