<?php

namespace RzlApp\BladeMinify\BladeCompiler;

use Illuminate\View\Compilers\BladeCompiler;

class IgnoreMinifyBladeCompiler extends BladeCompiler
{

  protected $openExcludeMinifyCount = 0;
  public const IGNORE_START = '<!--STARTED_IGNORE-->';
  public const IGNORE_END   = '<!--ENDED_IGNORE-->';

  public function compileString($value)
  {
    $result = parent::compileString($value);

    if ($this->openExcludeMinifyCount > 0) {
      throw new \Exception('Unclosed @ignoreMinify directive detected.');
    }

    return $result;
  }

  public function compileExcludeMinify($expression): string
  {
    $this->openExcludeMinifyCount++;

    return "<?php echo '" . self::IGNORE_START . "'; ?>";
  }

  public function compileEndExcludeMinify($expression)
  {
    if ($this->openExcludeMinifyCount == 0) {
      throw new \Exception('Unexpected @endIgnoreMinify directive detected.');
    }

    $this->openExcludeMinifyCount--;

    return "<?php echo '" . self::IGNORE_END . "'; ?>";
  }
}
