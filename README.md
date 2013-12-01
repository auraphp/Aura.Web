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
        "aura/web": "2.*@dev"
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

### Instantiation

First, instantiate a _WebFactory_ object, then use it to create _Request_ and
_Response_ objects.

```php
<?php
use Aura\Web\WebFactory;

$web_factory = new WebFactory($GLOBALS);
$request = $web_factory->newRequest();
$response = $web_factory->newResponse();
?>
```

Because each object contains so much functionality, we have split up the
documentation into a [Request](README-REQUEST.md) page and a
[Response](README-RESPONSE.md) page.

By way of overview, the _Request_ object has these sub-objects ...

- [$request->cookies](README-REQUEST.md#superglobals) for $_COOKIES
- [$request->env](README-REQUEST.md#superglobals) for $_ENV
- [$request->files](README-REQUEST.md#superglobals) for $_FILES
- [$request->post](README-REQUEST.md#superglobals) for $_POST
- [$request->query](README-REQUEST.md#superglobals) for $_GET
- [$request->server](README-REQUEST.md#superglobals) for $_SERVER
- [$request->client](README-REQUEST.md#client) for the client making the
  request
- [$request->content](README-REQUEST.md#content) for the raw body of the
  request
- [$request->headers](README-REQUEST.md#headers) for the request headers
- [$request->method](README-REQUEST.md#method) for the request method
- [$request->accept](README-REQUEST.md#accept) for content negotiation
- [$request->params](README-REQUEST.md#params) for path-info parameters
- [$request->url](README-REQUEST.md#url) for the request URL

... and the _Response_ object has these sub-objects:

- [$response->status](README-RESPONSE.md#status) for the status code, status
  phrase, and HTTP version
- [$response->headers](README-RESPONSE.md#headers) for non-cookie headers
- [$response->cookies](README-RESPONSE.md#cookies) for cookie headers
- [$response->content](README-RESPONSE.md#content) for describing the response
  content, and for convenience methods related to content type, charset,
  disposition, and filename
- [$response->cache](README-RESPONSE.md#cache) for convenience methods related
  to cache headers
- [$response->redirect](README-RESPONSE.md#redirect) for convenience methods
  related to Location and Status

Once you have built a _Response_ you can send it with any HTTP mechanism you
prefer, [including plain PHP](README-RESPONSE.md#sending-the-response).

Be sure to read the [Request](README-REQUEST.md) and
[Response](README-RESPONSE.md) pages for more detailed information.
