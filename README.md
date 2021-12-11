# php-thx
[![Release](https://img.shields.io/github/release/Fundevogel/php-thx.svg)](https://github.com/Fundevogel/php-thx/releases) [![License](https://img.shields.io/github/license/Fundevogel/php-thx.svg)](https://github.com/Fundevogel/php-thx/blob/main/LICENSE) [![Issues](https://img.shields.io/github/issues/Fundevogel/php-thx.svg)](https://github.com/Fundevogel/php-thx/issues) [![Status](https://travis-ci.org/Fundevogel/php-thx.svg?branch=main)](https://travis-ci.org/Fundevogel/php-thx)

A very simple PHP library for acknowledging the people behind your frontend dependencies - and giving thanks.


## Getting started

Install this package with [Composer](https://getcomposer.org):

```text
composer require Fundevogel/php-thx
```

**Note:**
For yarn v2 support, the `php-yaml` package is required!


## Usage

First, determine the paths to your files (datafile & lockfile, see below):

```php
<?php

require_once('vendor/autoload.php');

use Fundevogel\Thx;

$pkgFile = 'path/to/composer.json';                # or 'package.json' for NPM / Yarn
$lockFile = 'path/to/composer.lock'                # or 'package-lock.json' for NPM / 'yarn.lock' for Yarn
$cacheDriver = 'file';                             # Optional: Cache driver, see below
$cacheSettings = ['storage' => '/path/to/cache'];  # Optional: Cache settings, see below
```

**Note:**
For available cache drivers & settings, see [here](https://github.com/terrylinooo/simple-cache)!

Passing these options to `new Thx()` creates an instance:

```php
$obj = new Thx($pkgFile, $lockFile, $cacheDriver, $cacheSettings);
```

.. which you may configure to your liking by using:

- `setTimeout(int $seconds)`
- `setCacheDuration(int $days)`
- `setUserAgent(string $userAgent)`
- `setBlockList(array $blockList)`

```php
# For example:

$obj->setCacheDuration(7);                    # Cache results for one week
$obj->setBlockList(['php', 'some/library']);  # Block from being processed
```

After setting everything up, `giveBack()` makes some API calls & returns processed data, wrapped by a `Packages` object:

- Composer packages @ https://repo.packagist.org
- Node packages @ https://api.npms.io

```php
$processed = $obj->giveBack();
```

At this point, there are three basic methods you can use:

- `data()` returns raw data from lockfile for all used packages
- `pkgs()` returns processed data for all used packages
- `packages()` returns the names of all used packages

```php
# Dump raw data
$raw = $obj->data();

# Process data
$processed = $obj->giveBack();

# Work with it
$pkgData = $processed->pkgs();
```

For convenience, there are methods to

- list licenses & number of occurences: `licenses()`
- group packages by license: `byLicense()`

```php
$licenseData = $processed->licenses();
$groupedByLicense = $processed->byLicense();
```


## Example

This example should get you started:

```php
<?php

require_once('vendor/autoload.php');

use Fundevogel\Thx;

$pkgFile = 'path/to/composer.json';  # or 'package.json' for NPM / Yarn
$lockFile = 'path/to/composer.lock'  # or 'package-lock.json' for NPM / 'yarn.lock' for Yarn

try {
    $obj = new Thx($pkgFile, $lockFile);

    # Dump (raw) data extracted from lockfiles
    var_dump($obj->data());

    # Process data
    $processed = $obj->giveBack();

    # Dump package data
    var_dump($processed->pkgs())

    # Dump package names
    var_dump($processed->packages())

} catch (Exception $e) {
    # No dependencies found, file not found, ..
    echo $e->getMessage();
}
```


## Roadmap

- [x] ~~Add (more sophisticated) tests~~ for now, they get the job done
- [x] Parse yarn v1 lockfiles
- [x] Gather information using public APIs
- [x] Custom `Exception`s
- [x] Move data manipulation to uniform `Packages` class
- [ ] Provide more (sorting/filtering) methods, eg ..
    - [x] .. `byLicense()` = 'MIT' => [...], 'GPL v3' => [...] etc
    - .. `byDownloads()` = '2k' => [...], '1k' => [...] etc


## Credits

Most of the helper functions were taken from [Kirby](https://getkirby.com)'s excellent [`toolkit`](https://github.com/getkirby-v2/toolkit) package by [Bastian Allgeier](https://github.com/bastianallgeier) (who's just awesome, btw).


**Happy coding!**
