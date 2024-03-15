# Static Assets for Laravel

This package enables your Laravel app to use your compiled assets (e.g. js & css files) generated and hosted with Static Assets.

To learn about what Static Assets can do for you, visit [staticassets.app](https://staticassets.app).

## Requirements

* PHP >= 8.1
* Laravel >= 9.0

## Features

* Use remotely generated and hosted Vite or Mix compiled files.
* Choose between storing updated Vite / Mix manifest file on disk or in cache.

## Installation

```bash
composer require static-assets/laravel
```

You'll also need to install the Vite or Mix package for your Laravel app.

```bash
npm install @static-assets/vite
# or
npm install @static-assets/mix
```

For more information on these packages, checkout their docs ([Vite](https://github.com/StaticAssets/static-assets-vite) / [Mix](https://github.com/StaticAssets/static-assets-laravel-mix)).

## Configuration

The package can be configured using the following `.env` variables. 

By default, none of the following variables are required. Default behaviours are defined below.

### Enable / Disable the Package

```bash
STATIC_ASSETS=true
```

Default: `true` when `APP_ENV` is `production`, `false` otherwise.

### Manifest Storage

We store the manifest compiled by Static Assets either on your default disk or in the cache.

Valid values are: `disk` or `cache`.

```bash
STATIC_ASSETS_STORAGE=disk
```

By default, the Static Assets compiled manifest will be stored on the default disk.

### Manifest Cache Timeout (Cache only)

When storing the Static Assets manifest in the cache, you can specify the number of days the manifest should be cached for.

```bash
STATIC_ASSETS_CACHE_TIMEOUT=90
```

By default, the Static Assets compiled manifest will be cached for 30 days.

### Specify Release

We reference static assets by their git hash. Here you're able to define a specific release to use.

It's expected that this is likely only useful when debugging or testing.

```bash
STATIC_ASSETS_RELEASE=0e5eb38d172ceed3735ecae5a02767c8c945b31c
```

By default, the latest git commit hash is used.

```php
trim(exec('git --git-dir '.base_path('.git').' rev-parse HEAD'))
```

### Manifest Custom Directory

Should your Vite/Mix manifest not be stored in their default locations (/public or /public/build), you can specify the directory here.

```
# This non-default example will look for the manifest file in the 
# /build directory of your project.

STATIC_ASSETS_DIRECTORY=build
```

By default, the package will look for the manifest file in the `/public` directory.

### Publishing Configuration File

Whilst not required you can optionally also publish the configuration file using the following command:

```bash 
php artisan vendor:publish --tag=static-assets-config
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

We welcome contributions to this package. Please submit a Pull Request and we'll review.

## Security Vulnerabilities

We take security seriously at Static Assets. Any security issues should be reported to [security@staticassets.app](mailto:security@staticassets.app).

## Credits

Static Assets is a product of [Init Development Studios](https://initdevelopmentstudios.com/). We're proud members of the Laravel community and sponsor our local Laravel conference [Laracon AU](https://laracon.au/).

- [Benjamin Ayles](https://github.com/parkourben99)
- [Aaron Heath](https://aaronheath.com/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
