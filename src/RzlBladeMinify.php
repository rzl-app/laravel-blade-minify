<?php

namespace RzlApp\BladeMinify;

use RzlApp\BladeMinify\BladeCompiler\IgnoreMinifyBladeCompiler;

class RzlBladeMinify
{
  /**
   * @param string|null $html
   * @return string
   */
  public function minifierBlade(?string $html): string
  {
    $replace = [

      //remove tabs before and after HTML tags
      '/\>[^\S ]+/s'               => '>',
      '/[^\S ]+\</s'               => '<',

      //shorten multiple whitespace sequences; keep new-line characters because they matter in JS!!!
      '/([\t ])+/s'                => ' ',

      //remove leading and trailing spaces
      '/^([\t ])+/m'               => '',
      '/([\t ])+$/m'               => '',

      // remove JS line comments (simple only); do NOT remove lines containing URL (e.g. 'src="http://server.com/"')!!!
      '~//[a-zA-Z0-9 ]+$~m'        => '',

      //remove empty lines (sequence of line-end and white-space characters)
      '/[\r\n]+([\t ]?[\r\n]+)+/s' => "\n",

      //remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
      '/\>[\r\n\t ]+\</s'          => '><',

      //remove "empty" lines containing only JS's block end character; join with next line (e.g. "}\n}\n</script>" --> "}}</script>"
      '/}[\r\n\t ]+/s'             => '}',
      '/}[\r\n\t ]+,[\r\n\t ]+/s'  => '},',

      //remove new-line after JS's function or condition start; join with next line
      '/\)[\r\n\t ]?{[\r\n\t ]+/s' => '){',
      '/,[\r\n\t ]?{[\r\n\t ]+/s'  => ',{',

      //remove new-line after JS's line end (only most obvious and safe cases)
      '/\),[\r\n\t ]+/s'           => '),',

      //remove quotes from HTML attributes that does not contain spaces; keep quotes around URLs!
      //'~([\r\n\t ])?([a-zA-Z0-9]+)=\"([a-zA-Z0-9_\\-]+)\"([\r\n\t ])?~s'  => '$1$2=$3$4',
      '/(\n|^)(\x20+|\t)/'         => "\n",
      '/(\n|^)\/\/(.*?)(\n|$)/'    => "\n",
      '/\n/'                       => " ",
      '/\<\!--.*?-->/'             => "",

      # Delete multispace (Without \n)
      '/(\x20+|\t)/'               => " ",

      # strip whitespaces between tags
      '/\>\s+\</'                  => "><",

      # strip whitespaces between quotation ("') and end tags
      '/(\"|\')\s+\>/'             => "$1>",

      # strip whitespaces between = "'
      '/=\s+(\"|\')/'              => "=$1",

      # remove space after and before tags
      '/\>(\s++)?(.*?)(\s++)?</s' => ">$2<"

    ];

    // Find sections to exclude
    preg_match_all("/" . IgnoreMinifyBladeCompiler::IGNORE_START . "(.*?)" . IgnoreMinifyBladeCompiler::IGNORE_END . "/s", $html, $matches);

    // Replace sections to exclude with placeholders
    foreach ($matches[0] as $index => $exclude) {
      $html = str_replace($exclude, "%%%EXCL_$index%%%", $html);
    }

    $html = preg_replace(array_keys($replace), array_values($replace), (string) $html);

    // Restore the excluded sections
    foreach ($matches[0] as $index => $exclude) {
      $html = str_replace("%%%EXCL_$index%%%", $exclude, $html);
    }

    return $this->clearingCommentExcluded($html);
  }

  public function minifierBladeDisabled(?string $html): string
  {
    return $this->clearingCommentExcludedAtDisable($html);
  }

  private function clearingCommentExcluded($input)
  {
    $start = IgnoreMinifyBladeCompiler::IGNORE_START;
    $end = IgnoreMinifyBladeCompiler::IGNORE_END;

    $announcer = '<rzl-route-announcer style="position: absolute;"><div aria-live="assertive" id="__rzl-route-announcer__" role="alert" style="position: absolute; border: 0px; height: 1px; margin: -1px; padding: 0px; width: 1px; clip: rect(0px, 0px, 0px, 0px); overflow: hidden; white-space: nowrap; overflow-wrap: normal;"></div></rzl-route-announcer>';

    $search = [
      '/\>(\s++)?' . $start . '(\s++)?</s',
      '/\>(\s++)?' . $end . '(\s++)?</',
      '/\<\/div\>(\s++)?<script id="__server_mutation_"(.*?)\>(\s++)?(.*?)(\s++)?\<\/script>(\s++)?<script id="_RZL_DATA_"(.*?)\>(.*?)\<\/script>/',
    ];

    $replace = [
      '>' .
        '<',
      '>' .
        '<',
      '</div>' .
        '<script id="__server_mutation_"$2>$4</script><script id="_RZL_DATA_"$7>$8</script>'
    ];

    return str($input)->replaceMatches($search, $replace);
  }


  private function clearingCommentExcludedAtDisable($input)
  {
    return str_replace(
      [IgnoreMinifyBladeCompiler::IGNORE_START, IgnoreMinifyBladeCompiler::IGNORE_END],
      '',
      $input
    );
  }
}
