<?php

namespace RzlApp\BladeMinify\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use RzlApp\BladeMinify\RzlBladeMinifyFacade;

class RzlBladeMinify
{
  /**
   * @param Request $request
   * @param Closure $next
   * @return mixed
   */
  public function handle($request, Closure $next): mixed
  {
    /** @var Response */
    $response = $next($request);

    if (
      $this->isResponseObject($response)
      && $this->isResponseHtml($response)
      && !$this->isIgnoredRoute($request)
    ) {
      $html = str($response->getContent())->toString();

      $content = null;

      if (!config('rzl-blade-minify.enable')) {
        $content = RzlBladeMinifyFacade::bladeMinifyDisable($html);
      } elseif (config('rzl-blade-minify.run_production_only') && !app()->isProduction()) {
        $content = RzlBladeMinifyFacade::bladeMinifyDisable($html);
      } else {
        if (!app()->isProduction()) {
          $html = str($html)->replace(["%5B", "%5D"], ["[", "]"])->toString();
        }
        $content = RzlBladeMinifyFacade::bladeMinify($html);
      }

      $response->setContent($content);
    }

    return $response;
  }

  /**
   * @param Response $response
   * @return bool
   */
  protected function isResponseObject($response): bool
  {
    return is_object($response) && $response instanceof Response;
  }

  /**
   * @param Response $response
   * @return bool
   */
  protected function isResponseHtml(Response $response): bool
  {
    return strtolower(strtok($response->headers->get('Content-Type'), ';')) === 'text/html';
  }

  /**
   * @param Request $request
   * @return bool
   */
  protected function isIgnoredRoute($request): bool
  {
    return $request->route() && in_array($request->route()->getName(), config('rzl-blade-minify.ignore_route_name', []));
  }
}
