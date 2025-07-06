# ⚡️Rzl Laravel Blade HTML Minifier 🚀

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rzl-app/laravel-html-minify.svg?style=flat-rounded)](https://packagist.org/packages/rzl-app/laravel-html-minify)
[![Total Downloads](https://img.shields.io/packagist/dt/rzl-app/laravel-html-minify.svg?style=flat-rounded)](https://packagist.org/packages/rzl-app/laravel-html-minify)
[![PHPStan](https://img.shields.io/badge/phpstan-level%208-brightgreen?style=flat-rounded)](https://phpstan.org)
[![PHP](https://img.shields.io/badge/PHP-^8.2-blue?style=flat-rounded)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/Laravel-^10.x%20|%20^11.x%20|%20^12.x-red?style=flat-rounded)](https://laravel.com)
[![Illuminate Support](https://img.shields.io/badge/illuminate%2Fsupport-^10.x%20|%20^11.x%20|%20^12.x-blue?style=flat-rounded)](https://laravel.com/docs)

> 🚀 **Automatically minifies your Laravel Blade HTML output for smaller pages & blazing-fast load times.**
>
> 🛠 **Supports:**
>
> - 📚 [Laravel Docs](https://laravel.com/docs) — for official usage
> - 🧩 [`Illuminate\Support`](https://github.com/laravel/framework/tree/12.x/src/Illuminate/Support)
> - 🐘 PHP ^8.2 + Laravel ^10.x | ^11.x | ^12.x
>
> **Built with ❤️ by [@rzl-app](https://github.com/rzl-app).**

---

## 📚 Table of Contents

- 🛠 [Requirements](#requirements)
- ⚙️ [Installation](#installation)
- 🚀 [Setup](#setup)
- 🔥 [Usage](#usage)
- 📝 [Changelog](#changelog)
- 🤝 [Contributing](#contributing)
- 🛡 [Security](#security)
- 🙌 [Credits](#credits)
- 📜 [License](#license)
- 🔗 [Framework & Reference Links](#framework--reference-links)

---

<h2 id="requirements">🛠 Requirements</h2>

| Laravel Framework & `illuminate/support` | PHP  | Package |
| ---------------------------------------- | ---- | ------- |
| ^10.x \| ^11.x \| ^12.x                  | ^8.2 | v1.x    |

---

<h2 id="installation">⚙️ Installation</h2>

You can install the package via composer:

```bash
composer require rzl-app/laravel-html-minify
```

## Sponsor Laravel HTML Minifier on GitHub

[Become a sponsor to Rzl App
](https://github.com/sponsors/rzl-app).

---

<h2 id="setup">🚀 Setup</h2>

### Publish config

```php
php artisan vendor:publish --tag=RzlLaravelHtmlMinify
```

### Add middleware to web middleware group within app/Http/Kernel.php

```php
\RzlApp\BladeMinify\Middleware\RzlBladeMinify::class
```

---

<h2 id="usage">🔥 Usage</h2>

### Enable in .env

```php
RZL_MINIFY_ENABLE=true
```

### Disable in .env

```php
RZL_MINIFY_ENABLE=false
```

### Minify only in production

```php
RZL_MINIFY_ONLY_PROD=true
```

### Minify at all mode APP Env (default)

```php
RZL_MINIFY_ONLY_PROD=false
```

### Ignore specific route names from minifying render output

```php
'ignore_route_name' => [
  // 'dashboard',
  // 'home',
]
```

### Minify a particular HTML string manually

```php
RzlBladeMinifyFacade::htmlMinify("<div>...</div>");
```

### Ignoring minify a particular HTML string manually

```php
RzlBladeMinifyFacade::excludeHtmlMinify("<div>...</div>");
```

### Ignore minify in Blade

```php
@ignoreMinify
    <div> this script will ignored from minify   </div>
@endIgnoreMinify
```

---

<h2 id="changelog">📝 Changelog</h2>

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

---

<h2 id="contributing">🤝 Contributing</h2>

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

---

<h2 id="security">🛡 Security</h2>

Please report issues to [rizalvindwiky@gmail.com](mailto:rizalvindwiky@gmail.com).

---

<h2 id="credits">🙌 Credits</h2>

- [Rzl App](https://github.com/rzl-app)
- [All Contributors](../../contributors)

---

##

<h2 id="license">📜 License</h2>

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

<h2 id="framework--reference-links">🔗 Framework & Reference Links</h2>

| Reference            | URL                                                                                                                                            |
| -------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------- |
| 📝 Laravel Docs      | [https://laravel.com/docs](https://laravel.com/docs)                                                                                           |
| 🏗 Illuminate\Support | [https://github.com/laravel/framework/tree/12.x/src/Illuminate/Support](https://github.com/laravel/framework/tree/12.x/src/Illuminate/Support) |
| 🐘 PHP Official      | [https://www.php.net](https://www.php.net)                                                                                                     |

---

✅ **Enjoy `rzl-app/laravel-blade-minify`?**  
Leave a ⭐ on GitHub — it keeps this project thriving!

---

✨ From [rzl-app](https://github.com/rzl-app) — _where code meets passion._
