# php-thx
[![License](https://badgen.net/badge/license/GPL/blue)](https://codeberg.org/fundevogel/php-thx/src/branch/main/LICENSE) [![Packagist](https://badgen.net/packagist/v/fundevogel/php-thx)](https://packagist.org/packages/fundevogel/php-thx) [![Build](https://ci.codeberg.org/api/badges/Fundevogel/php-thx/status.svg)](https://codeberg.org/fundevogel/php-thx/issues)

A very simple PHP library for acknowledging the people behind your dependencies - and giving thanks.


## Getting started

Install this package with [Composer](https://getcomposer.org):

```text
composer require fundevogel/php-thx
```


## Usage

Spreading love & giving back should be easy, like this:

```php
use Fundevogel\Thx\ThankYou;

# Define paths to necessary files
$dataFile = 'path/to/composer.json';  # .. 'package.json'
$lockFile = 'path/to/composer.lock'   # .. 'package-lock.json' or 'yarn.lock'

try {
    # Extract & extend dataset
    $data = ThankYou::veryMuch($dataFile, $lockFile);

} catch (Exception $e) {
    # No dependencies found, file not found, ..
    echo $e->getMessage();
}
```

.. and in case you want to have more control, instantiate the appropriate `Driver` & configure it as needed:

```php
use Fundevogel\Thx\ThankYou;

$driver = ThankYou::haveFun($dataFile, $lockFile);

# Configuration for API calls
$driver->timeout = 3600  # request timeout (in seconds)
$driver->userAgent = 'YoursSincerely'  # request UA string
```

By themselves, the files you already have don't yield much information (mostly package name & installed version), yet this is all we need to know to .. make some API calls (which is done automatically):

- Composer packages @ https://repo.packagist.org
- Node packages @ https://api.npms.io

**Note**: As always when requesting data from third-parties, make sure to implement some kind of caching so you don't get blocked or exceed whatever limit they impose, and remember: going easy on somebody else's ressources (especially when they're provided for free) shows that you care, and that's always worth striving for.


## Roadmap

- [x] Add support for `pnpm`
- [ ] Check out v3 npm `lockfileVersion`


## Credits

Most of the helper functions were taken from [Kirby](https://getkirby.com)'s excellent [`toolkit`](https://github.com/getkirby-v2/toolkit) package by [Bastian Allgeier](https://github.com/bastianallgeier) (who's just awesome, btw).

**Happy coding!**
