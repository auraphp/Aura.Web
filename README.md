# Aura.Web

Provides web _Request_ and _Response_ objects for use by web controllers.
These are representations of the PHP web environment, not HTTP request and
response objects proper.

## Foreword

### Installation and Autoloading

This library is installable and autoloadable via Composer with the following
`require` element in your `composer.json` file:

    "require": {
        "aura/web": "dev-develop-2"
    }
    
Alternatively, download or clone this repository, then require or include its
_autoload.php_ file.

### Dependencies and PHP Version

As with all Aura libraries, this library has no userland dependencies. It
requires PHP version 5.3 or later.

### Tests

[![Build Status](https://travis-ci.org/auraphp/Aura.Web.png?branch=develop-2)](https://travis-ci.org/auraphp/Aura.Web)

This library has 100% code coverage with [PHPUnit][]. To run the tests at the
command line, go to the _tests_ directory and issue `phpunit`.

[phpunit]: http://phpunit.de/manual/

### PSR Compliance

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md


## Getting Started

TBD.
