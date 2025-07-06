<?php

return [
  /*
  |--------------------------------------------------------------------------
  | Env Variable for Minify Blade
  |--------------------------------------------------------------------------
  |
  | Set this value to the false if you don't need minify the blade
  | this is by default "true"
  |
  */
  'enabled'       => env('MINIFY_ENABLED', true),

  // exclude route name for exclude from blade minify
  'exclude_route' => [
    // 'routeName'
  ]
];
