<?php

namespace RzlApp\BladeMinify\VendorRewrites\Vite;

use Illuminate\Support\Arr;
use \Illuminate\Foundation\Vite;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class ViteCustom extends Vite
{
  /** @var bool */
  private $isUseAsyncOrDefer = false;
  /** @var bool */
  private $isSetAsync = false;
  /** @var bool */
  private $isSetDefer = false;

  /** @var array */
  private $persistenceRzlAttr = [];
  /** @var array */
  private $preferredData = [];

  /**
   * Generate Vite tags for an entrypoint.
   *
   * @param  string|string[]  $entrypoints
   * @param  string|null  $buildDirectory
   * @return \Illuminate\Support\HtmlString
   *
   * @throws \Exception
   */
  public function __invoke($entrypoints, $buildDirectory = null)
  {
    $this->persistenceRzlAttr = Arr::collapse([
      ["rzl-app" => true],
      [
        ...($this->isUseAsyncOrDefer === true ? $this->preferredData : [
          // "async" => true
        ])
      ]
    ]);

    $entrypoints = collect($entrypoints);
    $buildDirectory ??= $this->buildDirectory;

    if (!app()->isProduction() && $this->isRunningHot()) {
      return new HtmlString(
        $entrypoints
          ->prepend('@vite/client')
          ->map(fn($entrypoint) => $this->makeTagForChunk($entrypoint, $this->hotAsset($entrypoint), null, null))
          ->join('')
      );
    }

    $manifest = $this->manifest($buildDirectory);

    $tags = collect();
    $preloads = collect();

    foreach ($entrypoints as $entrypoint) {
      $chunk = $this->chunk($manifest, $entrypoint);

      $preloads->push([
        $chunk['src'],
        $this->assetPath("{$buildDirectory}/{$chunk['file']}"),
        $chunk,
        $manifest,
      ]);

      foreach ($chunk['imports'] ?? [] as $import) {
        $preloads->push([
          $import,
          $this->assetPath("{$buildDirectory}/{$manifest[$import]['file']}"),
          $manifest[$import],
          $manifest,
        ]);

        foreach ($manifest[$import]['css'] ?? [] as $css) {
          $partialManifest = Collection::make($manifest)->where('file', $css);

          $preloads->push([
            $partialManifest->keys()->first(),
            $this->assetPath("{$buildDirectory}/{$css}"),
            $partialManifest->first(),
            $manifest,
          ]);

          $tags->push($this->makeTagForChunk(
            $partialManifest->keys()->first(),
            $this->assetPath("{$buildDirectory}/{$css}"),
            $partialManifest->first(),
            $manifest
          ));
        }
      }

      $tags->push($this->makeTagForChunk(
        $entrypoint,
        $this->assetPath("{$buildDirectory}/{$chunk['file']}"),
        $chunk,
        $manifest
      ));

      foreach ($chunk['css'] ?? [] as $css) {
        $partialManifest = Collection::make($manifest)->where('file', $css);

        $preloads->push([
          $partialManifest->keys()->first(),
          $this->assetPath("{$buildDirectory}/{$css}"),
          $partialManifest->first(),
          $manifest,
        ]);

        $tags->push($this->makeTagForChunk(
          $partialManifest->keys()->first(),
          $this->assetPath("{$buildDirectory}/{$css}"),
          $partialManifest->first(),
          $manifest
        ));
      }
    }

    [$stylesheets, $scripts] = $tags->unique()->partition(fn($tag) => str_starts_with($tag, '<link'));

    $preloads = $preloads->unique()
      ->sortByDesc(fn($args) => $this->isCssPath($args[1]))
      ->map(fn($args) => $this->makePreloadTagForChunk(...$args));

    return new HtmlString($preloads->join('') . $stylesheets->join('') . $scripts->join(''));
  }

  /**
   * Set the Vite "hot" file path.
   *
   * @param  string|null $pathHot default = `config('app.hot_file')`
   * @return $this
   */
  public function useHotFile($pathHot = null)
  {
    if (str($pathHot)->trim()->lower()->is(["null", null]) || is_null($pathHot)) {
      $this->hotFile = config('app.hot_file');
    } else {
      $this->hotFile = $pathHot;
    }

    return $this;
  }

  /**
   * Set the Vite "hot on viteReactRefresh" file path.
   *
   * @param  string|null $pathHot
   * @return $this
   */
  public function useHotFileRefresh($pathHot = null, $dataAttribute = [])
  {
    return self::useHotFile($pathHot);
  }

  /** ---------------------------------
   * * Using Async or Defer on Default
   * ---------------------------------
   *
   * @param string $preferredParams #default = "unset", valid value is (`"async" |"defer" |"unset" | false`), if `$preferredParams` is `false` is mean same as `"unset"` and if value of `$preferredParams` is `invalid | empty | null`, will return as `"unset"`.
   */
  public function useAsyncOrDeferDefault($preferredParams = "unset")
  {
    $this->isUseAsyncOrDefer = true;

    $preferred = str($preferredParams)->trim()->lower();

    if (filled($preferred->toString()) && $preferred->is("defer")) {
      $this->isSetAsync = false;
      $this->isSetDefer = true;
      $this->preferredData = ["defer" => true];

      return $this;
    }

    if (filled($preferred->toString()) && $preferred->is("async")) {
      $this->isSetAsync = true;
      $this->isSetDefer = false;
      $this->preferredData = ["async" => true];

      return $this;
    }

    $this->isSetAsync = false;
    $this->isSetDefer = false;
    $this->preferredData = [];
    return $this;

    return $this;
  }

  public function isCssPath($url)
  {
    return parent::isCssPath($url);
  }

  /**
   * Make tag for the given chunk.
   *
   * @param  string  $src
   * @param  string  $url
   * @param  array|null  $chunk
   * @param  array|null  $manifest
   * @return string
   */
  protected function makeTagForChunk($src, $url, $chunk, $manifest)
  {
    if (
      $this->nonce === null
      && $this->integrityKey !== false
      && !array_key_exists($this->integrityKey, $chunk ?? [])
      && $this->scriptTagAttributesResolvers === []
      && $this->styleTagAttributesResolvers === []
    ) {
      if ($this->isCssPath($url)) {
        return $this->makeStylesheetTagWithAttributes($url, []);
      }

      return $this->makeScriptTagWithAttributes($url, []);
    }

    if ($this->isCssPath($url)) {
      $attrCss = $this->resolveStylesheetTagAttributes($src, $url, $chunk, $manifest);

      return $this->makeStylesheetTagWithAttributes(
        $url,
        array_merge(
          Arr::except($attrCss, ["rzl-app"]),
          [
            "rzl-app" => true,
          ]
        )
      );
    }

    $attrScript = $this->resolveScriptTagAttributes($src, $url, $chunk, $manifest);

    return $this->makeScriptTagWithAttributes(
      $url,
      array_merge(
        Arr::except($attrScript, ["rzl-app"]),
        [
          "rzl-app" => true,
        ]
      )
    );
  }

  /**
   * Resolve the attributes for the chunks generated preload tag.
   *
   * @param  string  $src
   * @param  string  $url
   * @param  array  $chunk
   * @param  array  $manifest
   * @return array|false
   */
  protected function resolvePreloadTagAttributes($src, $url, $chunk, $manifest)
  {
    $attributes = $this->isCssPath($url) ? array_merge(
      [
        'rel' => 'preload',
        'as' => 'style',
        'href' => $url,
        'nonce' => $this->nonce ?? false,
        'crossorigin' => $this->resolveStylesheetTagAttributes($src, $url, $chunk, $manifest)['crossorigin'] ?? false,
      ],
    ) : array_merge(
      [
        'rel' => 'modulepreload',
        'href' => $url,
        'nonce' => $this->nonce ?? false,
        'crossorigin' => $this->resolveScriptTagAttributes($src, $url, $chunk, $manifest)['crossorigin'] ?? false,
      ],
    );

    $attributes = $this->integrityKey !== false
      ? array_merge($attributes, ['integrity' => $chunk[$this->integrityKey] ?? false])
      : $attributes;

    foreach ($this->preloadTagAttributesResolvers as $resolver) {
      if (false === ($resolvedAttributes = $resolver($src, $url, $chunk, $manifest))) {
        return false;
      }

      $attributes =  array_merge(
        $attributes,
        self::resolvePersistenceRzlAtr($resolvedAttributes),
        Arr::except($resolvedAttributes, [
          "integrity",
          "rel",
          "as",
          "href",
          "nonce",
          "crossorigin",
          "async",
          "defer",
          "rzl-app",
        ])
      );
    }

    return collect($this->preloadTagAttributesResolvers)->isEmpty() ?
      array_merge(
        Arr::except($attributes, [
          "async",
          "defer",
        ]),
        self::resolvePersistenceRzlAtr($attributes),
      ) :
      $attributes;
  }

  /**
   * Resolve the attributes for the chunks generated script tag.
   *
   * @param  string  $src
   * @param  string  $url
   * @param  array|null  $chunk
   * @param  array|null  $manifest
   * @return array
   */
  protected function resolveScriptTagAttributes($src, $url, $chunk, $manifest)
  {
    $attributes = $this->integrityKey !== false
      ? ['integrity' => $chunk[$this->integrityKey] ?? false]
      : [];

    foreach ($this->scriptTagAttributesResolvers as $resolver) {
      $attributes = array_merge(
        $attributes,
        Arr::except($resolver($src, $url, $chunk, $manifest), ["integrity"])
      );
    }

    return $attributes;
  }

  /**
   * Resolve the attributes for the chunks generated stylesheet tag.
   *
   * @param  string  $src
   * @param  string  $url
   * @param  array|null  $chunk
   * @param  array|null  $manifest
   * @return array
   */
  protected function resolveStylesheetTagAttributes($src, $url, $chunk, $manifest)
  {
    $attributes = $this->integrityKey !== false
      ? ['integrity' => $chunk[$this->integrityKey] ?? false]
      : [];

    foreach ($this->styleTagAttributesResolvers as $resolver) {
      $attributes = array_merge(
        $attributes,
        Arr::except($resolver($src, $url, $chunk, $manifest), ["integrity"])
      );
    }

    return $attributes;
  }

  /**
   * Generate a link tag with attributes for the given URL.
   *
   * @param  string  $url
   * @param  array  $attributes
   * @return string
   */
  protected function makeStylesheetTagWithAttributes($url, $attributes)
  {
    $attributes = $this->parseAttributes(
      array_merge(
        [
          'rel' => 'stylesheet',
          'href' => $url,
          'nonce' => $this->nonce ?? false,
        ],
        self::resolvePersistenceRzlAtr($attributes),
        Arr::except($attributes, [
          "rel",
          "href",
          "nonce",
          "rzl-app",
          "async",
          "defer",
        ]),
      )
    );

    return '<link ' . implode(' ', $attributes) . ' />';
  }

  /**
   * Generate a script tag with attributes for the given URL.
   *
   * @param  string  $url
   * @param  array  $attributes
   * @return string
   */
  protected function makeScriptTagWithAttributes($url, $attributes)
  {
    $attributes = $this->parseAttributes(
      array_merge(
        [
          'type' => 'module',
          'src' => $url,
          'nonce' => $this->nonce ?? false,
        ],
        self::resolvePersistenceRzlAtr($attributes),
        Arr::except($attributes, [
          "type",
          "src",
          "nonce",
          "rzl-app",
          "async",
          "defer",
        ]),
      )
    );

    return '<script ' . implode(' ', $attributes) . '></script>';
  }

  /**
   * Generate React refresh runtime script.
   *
   * @return \Illuminate\Support\HtmlString|void
   */
  public function reactRefresh($pathHot = null, $dataAttribute = [])
  {
    if (($this->isRunningHot() && !app()->isLocal()) || !$this->isRunningHot()) {
      return;
    }

    $attributes = $this->parseAttributes(array_merge(
      [
        "type" => "module",
        "rzl-app" => true,
        'nonce' => $this->cspNonce(),
      ],
      Arr::except($dataAttribute, [
        "type",
        "ignore--minify",
        "nonce",
        "rzl-app",
        "async",
        "defer"
      ]),
      app()->isLocal() ? ["ignore--minify"] : [],
    ));

    return new HtmlString(
      sprintf(
        <<<'HTML'
          <script %s>
            import RefreshRuntime from "%s";
            RefreshRuntime.injectIntoGlobalHook(window);
            window.$RefreshReg$ = () => {};
            window.$RefreshSig$ = () => (type) => type;
            window.__vite_plugin_react_preamble_installed__ = true;
          </script>
          HTML,
        implode(' ', $attributes),
        $this->hotAsset('@react-refresh')
      )
    );
  }

  /**
   * @param array $attributes
   */
  protected function resolvePersistenceRzlAtr($attributes)
  {
    $persistenceRzlAttr = [];

    if (Arr::has($attributes, "defer")) {
      $persistenceRzlAttr = array_merge(
        Arr::except($this->persistenceRzlAttr, ["async"]),
        ["defer" => true]
      );
    } else if (Arr::has($attributes, "async")) {
      $persistenceRzlAttr = array_merge(
        Arr::except($this->persistenceRzlAttr, ["defer"]),
        ["async" => true]
      );
    } else {
      $persistenceRzlAttr = $this->persistenceRzlAttr;
    }

    return $persistenceRzlAttr;
  }

  /** ---------------------------------
   * * Get Full Path Manifest File Including Manifest File Name.
   * --------------------------------- */
  public function getManifestFile()
  {
    return "/" . config("app.build_dir") . "/" . config("app.manifest_name");
  }
}
