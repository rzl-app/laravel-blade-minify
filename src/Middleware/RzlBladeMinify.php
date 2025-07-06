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
      config('rzl-blade-minify.enabled')
      && $this->isResponseObject($response)
      && $this->isHtmlResponse($response)
      && !$this->isRouteExclude($request)
    ) {
      $html = str($response->getContent())->toString();

      if (!app()->isProduction()) {
        $html = str($html)->replace(["%5B", "%5D"], ["[", "]"])->toString();
      }
      $response->setContent(RzlBladeMinifyFacade::bladeMinify($html));
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
  protected function isHtmlResponse(Response $response): bool
  {
    return strtolower(strtok($response->headers->get('Content-Type'), ';')) === 'text/html';
  }

  /**
   * @param Request $request
   * @return bool
   */
  protected function isRouteExclude($request): bool
  {
    return $request->route() && in_array($request->route()->getName(), config('rzl-blade-minify.exclude_route', []));
  }
}
