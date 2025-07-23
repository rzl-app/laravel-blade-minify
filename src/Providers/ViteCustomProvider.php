<?php

namespace RzlApp\BladeMinify\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class ViteCustomProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    $this->app->bind(
      'Illuminate\Foundation\Vite', // original class that will be replaced
      'RzlApp\BladeMinify\VendorRewrites\Vite\ViteCustom', // custom class that will be injected
    );
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    $vite = Vite::useHotFile(config("app.hot_file"))
      ->createAssetPathsUsing(fn(string $path, ?bool $secure) =>  "/{$path}");

    if (app()->isLocal()) {
      $vite::class;
    }

    if (app()->isProduction()) {
      $vite
        ->useBuildDirectory(config("app.build_dir"))
        ->useManifestFilename(config("app.manifest_name"));
    }

    if ($this->app->resolved('blade.compiler')) {
      $this->registerDirective($this->app['blade.compiler']);
    } else {
      $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
        $this->registerDirective($bladeCompiler);
      });
    }
  }

  protected function registerDirective(BladeCompiler $bladeCompiler)
  {
    $bladeCompiler->directive('viteReactRefresh', function ($expression) {
      return "<?php echo e(Vite::useHotFileRefresh({$expression})->reactRefresh($expression)); ?>";
    });
  }
}
