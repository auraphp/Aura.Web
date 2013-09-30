# Aura.Web

Provides web _Request_ and _Response_ objects for use by web controllers.
These are representations of the PHP web environment, not HTTP request and
response objects proper.

## Foreword

### Requirements

This library requires PHP 5.3 or later, and has no userland dependencies.

### Installation

This library is installable and autoloadable via Composer with the following
`require` element in your `composer.json` file:

    "require": {
        "aura/web": "dev-develop-2"
    }
    
Alternatively, download or clone this repository, then require or include its
_autoload.php_ file.

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

### Request

Instantiate the _Request_ object as below

```php
use Aura\Web\Request;
use Aura\Web\Request\PropertyFactory;

$property_factory = new PropertyFactory(array(
    '_SERVER' => $_SERVER,
    '_GET' => $_GET,
    '_POST' => $_POST,
    '_FILES' => $_FILES,
    '_ENV' => $_ENV,
    '_COOKIE' => $_COOKIE
));
$request = new Request($property_factory);
```

The request object contains client, environement, files, headers, 
the raw input content, method, negotiate, params, post values, query values and
server values.

You can get the information of the requested method via `method` object
inside the _Request_.

- Available methods in _Method_ object are

    - `get()` get the requested method value
    - `isDelete` whether the requested method was delete
    - `isGet` whether the requested method was get
    - `isHead` whether the requested method was head
    - `isOptions` whether the requested method was options
    - `isPatch` whether the requested method was patch
    - `isPut` whether the requested method was put
    - `isPost` whether the requested method was post

Example usage 
    
```php
$request->method->isPost();
```

You can get `$_GET` values via _Query_ object in the _Request_ object

```php
// http://localhost/?name=Aura
$name = $request->query->get('name', 'defaut value');
```

If there is no `name` you will get the `default value`.
