# ⚡️Rzl Laravel HTML Minifier 🚀

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rzl-app/laravel-html-minify.svg?style=flat-rounded)](https://packagist.org/packages/rzl-app/laravel-html-minify)
[![Total Downloads](https://img.shields.io/packagist/dt/rzl-app/laravel-html-minify.svg?style=flat-rounded)](https://packagist.org/packages/rzl-app/laravel-html-minify)
[![PHPStan](https://img.shields.io/badge/phpstan-level%208-brightgreen?style=flat-rounded)](https://phpstan.org)

> This package helps to minify your project`s html (blade file) render output.
> **Built with ❤️ by [@rzl-app](https://github.com/rzl-app).**
> 🚀 **Minify your Laravel Blade HTML output automatically for smaller pages & faster load times.**  
> Built with ❤️ by [@rzl-app](https://github.com/rzl-app)

---

## 🛠 Requirements

| Laravel  | PHP     | Package |
| -------- | ------- | ------- |
| \>= 10.x | \>=8.1x | v 1.x   |

---

## ⚙️ Installation

You can install the package via composer:

```bash
composer require rzl-app/laravel-html-minify
```

## Sponsor Laravel HTML Minifier on GitHub

[Become a sponsor to Rzl App
](https://github.com/sponsors/rzl-app).

---

## Setup

### Publish config

```php
php artisan vendor:publish --tag=RzlLaravelHtmlMinify
```

### Add middleware to web middleware group within app/Http/Kernel.php

```php
\RzlApp\BladeMinify\Middleware\RzlBladeMinify::class
```

---

## 🚀 Usage

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

### Minify at all mode APP Env

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

### Testing

```bash
composer test
```

---

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

---

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

---

## 🛡 Security

Please report issues to [rizalvindwiky@gmail.com](mailto:rizalvindwiky@gmail.com).

---

## 🙌 Credits

- [Rzl App](https://github.com/rzl-app)
- [All Contributors](../../contributors)

---

## 📜 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---
