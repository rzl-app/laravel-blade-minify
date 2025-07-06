<?php

return [
  /*
  |--------------------------------------------------------------------------
  | Env Variable for Minify Blade
  |--------------------------------------------------------------------------
  |
  | Set this default value or add `RZL_MINIFY_ENABLE=false` at .env to
  |   the false if you want disable minifying render output.
  |
  | This is by default is "true"
  |
  */
  'enable'               => env('RZL_MINIFY_ENABLE', true),

  /*
  |--------------------------------------------------------------------------
  | Env Variable for Running Minify Blade Only On Production
  |--------------------------------------------------------------------------
  |
  | Set this default value or add `RZL_MINIFY_ONLY_PROD=true` at .env to 
  |   the true to avoiding minifying render output if not production.
  |
  | This is by default is "false"
  |
  */
  'run_production_only'  => env('RZL_MINIFY_ONLY_PROD', false),

  /*
  |--------------------------------------------------------------------------
  | Ignoring list route name for ignore from minifying render output.
  |--------------------------------------------------------------------------
  |
  | Set listing of Route Name to avoiding from minifying render output.
  |
  | This is by default is [].
  |
  */
  'ignore_route_name'    => [
    // 'dashboard',
    // 'home',
  ]
];
