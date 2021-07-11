# php-thx
[![Release](https://img.shields.io/github/release/S1SYPHOS/php-thx.svg)](https://github.com/S1SYPHOS/php-thx/releases) [![License](https://img.shields.io/github/license/S1SYPHOS/php-thx.svg)](https://github.com/S1SYPHOS/php-thx/blob/main/LICENSE) [![Issues](https://img.shields.io/github/issues/S1SYPHOS/php-thx.svg)](https://github.com/S1SYPHOS/php-thx/issues) [![Status](https://travis-ci.org/S1SYPHOS/php-thx.svg?branch=main)](https://travis-ci.org/S1SYPHOS/php-thx)

A very simple PHP library for acknowledging the people behind your frontend dependencies - and giving thanks.


## Getting started

Install this package with [Composer](https://getcomposer.org):

```text
composer require S1SYPHOS/php-thx
```

**Note:**
For yarn v2 support, the `php-yaml` package is required!


# Usage

This example should get you started:

```php
<?php

require_once('vendor/autoload.php');

use S1SYPHOS\Thx;

$pkgFile = 'path/to/composer.json';  # or 'package.json' for NPM / Yarn
$lockFile = 'path/to/composer.lock'  # or 'package-lock.json' for NPM / 'yarn.lock' for Yarn

try {
    $obj = Thx::giveBack($pkgFile, $lockFile)->spreadLove();

    # Dump package names
    var_dump($obj->packages())

    # Dump (raw) data extracted from lockfiles
    var_dump($obj->data())

} catch (Exception $e) {
    # No dependencies found, file not found, ..
    echo $e->getMessage();
}
```


## Roadmap

- [ ] Add (more sophisticated) tests
- [x] Gather information using public APIs
- [x] Custom `Exception`s
- [ ] Provide more methods
- [x] Parse yarn v1 lockfiles


## Credits

Most of the helper functions were taken from [Kirby](https://getkirby.com)'s excellent [`toolkit`](https://github.com/getkirby-v2/toolkit) package by [Bastian Allgeier](https://github.com/bastianallgeier) (who's just awesome, btw).


**Happy coding!**
