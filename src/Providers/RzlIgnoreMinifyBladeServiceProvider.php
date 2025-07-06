<?php

namespace RzlApp\BladeMinify\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use RzlApp\BladeMinify\BladeCompiler\IgnoreMinifyBladeCompiler;

class RzlIgnoreMinifyBladeServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap the application services.
   */
  public function boot(): void
  {
    /** @var \RzlApp\BladeMinify\BladeCompiler\ExcludeMinifyBladeCompiler */
    $compiler = app('blade.compiler');

    Blade::directive('ignoreMinify', function ($expression) use ($compiler) {
      return $compiler->compileExcludeMinify($expression);
    });
    Blade::directive('endIgnoreMinify', function ($expression) use ($compiler) {
      return $compiler->compileEndExcludeMinify($expression);
    });
  }

  /**
   * Register the application services.
   */
  public function register(): void
  {
    $this->app->singleton('blade.compiler', function ($app) {
      return new IgnoreMinifyBladeCompiler($app['files'], $app['config']['view.compiled']);
    });
  }
}
