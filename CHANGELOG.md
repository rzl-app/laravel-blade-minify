## Changelog

## All notable changes to `laravel-blade-minify` will be documented in this file.

## 1.1.0
### ADD NEW FEATURES AND CHANGE VARIABLE.

#### New Features:
  * Minifying only at Production mode APP Env:
    1. Add `RZL_MINIFY_ONLY_PROD=true` will avoiding minifying render output if not production, by default is `false`. 


#### Changes:
 * Env File:
   1. From `MINIFY_ENABLED` to `RZL_MINIFY_ENABLE`.

 * Config File:
   1. From `exclude_route` to `ignore_route_name`.
  
   ##### Notes: If you already installed from version `1.0.0` and then upgrade to `1.1.0` you need run `php artisan optimize:clear`.

 **Full Changelog**: https://github.com/rzl-app/laravel-blade-minify/compare/v1.0.0...v1.1.0
 
 ---
 
## 1.0.0

- Support till Laravel 12.
- Initial release.
