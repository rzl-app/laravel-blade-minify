<?php

namespace RzlApp\BladeMinify;

use Illuminate\Support\Facades\Facade;
use RzlApp\BladeMinify\BladeCompiler\IgnoreMinifyBladeCompiler;

class RzlBladeMinifyFacade extends Facade
{

  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor(): string
  {
    return 'rzl-blade-minify';
  }

  /**
   * @param $html
   * @return string
   */
  public static function bladeMinify(?string $html = NULL): string
  {
    return (new RzlBladeMinify())->minifierBlade((string) $html);
  }

  /**
   * @param $html
   * @return string
   */
  public static function bladeMinifyDisable(?string $html = NULL): string
  {
    return (new RzlBladeMinify())->minifierBladeDisabled((string) $html);
  }

  /**
   * @param $html
   * @return string
   */
  public static function excludeHtmlMinify(?string $html = NULL): string
  {
    return IgnoreMinifyBladeCompiler::IGNORE_START . (string) $html . IgnoreMinifyBladeCompiler::IGNORE_END;
  }
}
