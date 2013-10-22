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

### Request Object

The _Request_ object describes the current web execution context for PHP. Note
that it is **not** an HTTP request object proper, since it includes things
like `$_ENV` and various non-HTTP `$_SERVER` keys.

To create a _Request_ object, instantiate a _WebFactory_ and get new _Request_
object from it:

```php
<?php
use Aura\Web\WebFactory;

$web_factory = new WebFactory($GLOBALS);
$request = $web_factory->newRequest();
?>
```

The _Request_ object contains several property objects. Some represent a copy
of the PHP superglobals ...

- `$request->cookies` for `$_COOKIES`
- `$request->env` for `$_ENV`
- `$request->files` for `$_FILES`
- `$request->post` for `$_POST`
- `$request->query` for `$_GET`
- `$request->server` for `$_SERVER`

... and others represent more specific kinds of information about the request:

- `$request->client` for the client making the request
- `$request->content` for the raw body of the request
- `$request->headers` for the request headers
- `$request->method` for the request method
- `$request->negotiate` for content negotiation
- `$request->params` for path-info parameters
- `$request->url` for the request URL

The _Request_ object has only one method, `isXhr()`, to indicate if the
request is an _XmlHttpRequest_ or not.

#### Superglobals

Each of the superglobal representation objects has a single method, `get()`,
that returns the value of a key in the superglobal, or an alternative value
if the key is not present.  The values here are read-only.

```php
<?php
// returns the value of $_POST['field_name'], or 'not set' if 'field_name' is
// not present in $_POST
$field_name = $request->post->get('field_name', 'not set');

// if no key is given, returns an array of all values in the superglobal
$all_server_values = $request->server->get();

// the $_FILES array has been rearranged to look like $_POST
$file = $request->files->get('file_field', array());
?>
```

#### Client

The `$request->client` object has these methods:

- `getForwardedFor()` returns the values of the `X-Forwarded-For` headers as
  an array.

- `getReferer()` returns the value of the `Referer` header.

- `getIp()` returns the value of `$_SEVER['REMOTE_ADDR']`, or the appropriate
  value of `X-Forwarded-For`.

- `getUserAgent()` return the value of the `User-Agent` header.

- `isCrawler()` returns true if the `User-Agent` header matches one of a list
  of bot/crawler/robot user agents (otherwise false).
  
- `isMobile()` returns true if the `User-Agent` header matches one of a list
  of mobile user agents (otherwise false).

To add to the list of recognized user agents, set up the _WebFactory_ with
them first, then create the _Request_ object afterwards.

```php
<?php
$web_factory->setMobileAgents(array(
    'NewMobileAgent',
    'AnotherNewMobile',
));

$web_factory->setCrawlerAgents(array(
    'NewCrawlerAgent',
    'AnotherNewCrawler',
));

$request = $web_factory->newRequest();
?>
```

#### Content

The `$request->content` object has these methods:

- `getType()` returns the content-type of the request body

- `getRaw()` return the raw request body

- `get()` returns the request body after decoding it based on the content type

The _Content_ object has two decoders built in.
If the request specified a content type of `application/json`,
the `get()` method will automatically decode the body with `json_decode()`.
Likewise, if the content type is `application/x-www-form-urlencoded`, the
`get()` method will automatically decode the body with `parse_str()`.

If you want to add or change content decoders, set up the _WebFactory_ with
them first, then create the _Request_ object afterwards.

```php
<?php
// content-type => callable
$web_factory->setDecoders(array(
    'application/x-special-content-type' => function ($body) {
        // decoding logic
    },
));

$request = $web_factory->newRequest();
?>
```

#### Headers

The `$request->headers` object has a single method, `get()`, that returns the
value of a particular header, or an alternative value if the key is not
present. The values here are read-only.

```php
<?php
// returns the value of 'X-Header' if present, or 'not set' if not
$header_value = $request->post->get('X-Header', 'not set');
?>
```

#### Method

The `$request->method` object has these methods:

- `get()`: returns the request method value
- `isDelete()`: Did the request use a DELETE method?
- `isGet()`: Did the request use a GET method?
- `isHead()`: Did the request use a HEAD method?
- `isOptions()`: Did the request use an OPTIONS method?
- `isPatch()`: Did the request use a PATCH method?
- `isPut()`: Did the request use a PUT method?
- `isPost()`: Did the request use a POST method?

```php
<?php
if ($request->method->isPost()) {
    // perform POST actions
}
?>
```

You can also call `is*()` on the _Method_ object; the part after `is` is
treated as custom HTTP method name, and checks if the request was made using
that HTTP method.

```php
<?php
if ($request->method->isCustom()) {
    // perform CUSTOM actions
}
?>
```

Sometimes forms use a special field to indicate a custom HTTP method on a
POST. By default, the _Method_ object honors the `_method` form field.

```php
<?php
// a POST with the field '_method' will use the _method value instead of POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['_method'] = 'PUT';
$request = $web_factory->newRequest();
echo $request->method->get(); // PUT
?>
```

To set the form field used to indicate a custom HTTP method on a POST, set up
the _WebFactory_ with it first, then create the _Request_ object.

```php
<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['_http_method_override'] = 'DELETE';
$web_factory->setMethodField('_http_method_override');
$request = $web_factory->newRequest();
echo $request->method->get(); // DELETE
?>
```

#### Negotiate

The _Negotiate_ object helps with negotiating acceptable media (content)
types, character sets, encodings, and languages.

These `$request->negotiate` methods return the values indicated by the
request:

- `getAccept()` returns the `Accept` header value converted to an array
  arranged by quality level (this is for media types)
  
- `getAcceptCharset()` returns the `Accept-Charset` header value converted
  to an array arranged by quality level

- `getAcceptEncoding()` returns the `Accept-Encoding` header value converted
  to an array arranged by quality level

- `getAcceptLanguage()` returns the `Accept-Language` header value converted
  to an array arranged by quality level

You can negotiate between the what you have available, and what the request
indicates is acceptable, using the approprate `get*()` method.

- `getMedia()` negotiates the content type
- `getCharset()` negoatiates the character set
- `getLangauge()` negotiates the langauge code
- `getEncoding()` negotiates the encoding

For example:

```php
<?php
// assume the request indicates these Accept values (XML is best, then CSV,
// then anything else)
$_SERVER['HTTP_ACCEPT'] = 'application/xml;q=1.0,text/csv;q=0.5,*;q=0.1';

// create the request object
$request = $web_factory->newRequest();

// assume our application has `application/json` and `text/csv` available
// as content types, in order of highest-to-lowest preference for delivery
$available = array(
    'application/json',
    'text/csv',
);

// get the best match between what the request finds acceptable and what we
// have available; the result in this case is 'text/csv'
$content_type = $request->negotiate->getMedia($available);
?>
```

If the requested URL ends in a recognized file extension for a content type,
the _Negotiate_ object will use that file extension instead of the explicit
`Accept` header value to determine the acceptable content type for the
request.

```php
<?php
// assume the request indicates these Accept values (XML is best, then CSV,
// then anything else)
$_SERVER['HTTP_ACCEPT'] = 'application/xml;q=1.0,text/csv;q=0.5,*;q=0.1';

// assume also that the request URI explicitly notes a .json file extension
$_SERVER['REQUEST_URI'] = '/path/to/entity.json';

// create the request object
$request = $web_factory->newRequest();

// assume our application has `application/json` and `text/csv` available
// as content types, in order of highest-to-lowest preference for delivery
$available = array(
    'application/json',
    'text/csv',
);

// get the best match between what the request finds acceptable and what we
// have available; the result in this case is 'application/json' because of
// the file extenstion overriding the Accept header values
$content_type = $request->negotiate->getMedia($available);
?>
```

See the _Negotiate_ class file for the list of what file extensions map to 
what content types. To set your own mappings, set up the _WebFactory_ object
first, then create the _Request_ object.

```php
<?php
$web_factory->setTypes(array(
    '.foo' => 'application/x-foo-content-type',
));

$request = $web_factory->newRequest();
?>
```

#### Params

Unlike most _Request_ property objects, the _Params_ object is read-write (not
read-only). The _Params_ object allows you to set application-specific
parameter values. These are typically discovered by parsing a URL path through
a router of some sort (e.g. [Aura.Router][]).

  [Aura.Router]: https://github.com/auraphp/Aura.Router

The `$request->params` object has two methods:

- `set()` to set the array of parameters
- `get()` to get back a specific parameter, or the array of all parameters

For example:

```php
<?php
// parameter values discovered by a routing mechanism
$values = array(
    'controller' => 'blog',
    'action' => 'read',
    'id' => '88',
);

// set the parameters on the request
$request->params->set($values);

// get the 'id' param, or false if it is not present
$id = $request->params->get('id', false);

// get all the params as an array
$all_params = $request->params->get();
?>
```

#### Url

The `$request->url` object has two methods:

- `get()` returns the full URL string; or, if a component constant is passed,
  returns only that part of the URL

- `isSecure()` indicates if the request is secure, whether via SSL, TLS, or
  forwarded from a secure protocol

```php
<?php
// get the full URL string
$string = $request->url->get();

// get a particular part of the URL; for the component constants, see
// http://php.net/parse-url
$scheme   = $request->url->get(PHP_URL_SCHEME);
$host     = $request->url->get(PHP_URL_HOST);
$port     = $request->url->get(PHP_URL_PORT);
$user     = $request->url->get(PHP_URL_USER);
$pass     = $request->url->get(PHP_URL_PASS);
$path     = $request->url->get(PHP_URL_PATH);
$query    = $request->url->get(PHP_URL_QUERY);
$fragment = $request->url->get(PHP_URL_FRAGMENT);
?>
```

### Response Object

The _Response_ object describes the web response that should be sent to the
client. Note that it is **not** an HTTP response object proper; at best, it is
a series of hints to be used when building the HTTP response. This means that
setting values on the _Response_ object **does not** cause values to be sent
to the client; it can be inspected during testing to see if the correct values
have been set without generating output.

To create a _Response_ object, instantiate a _WebFactory_ and get new
_Request_ object from it:

```php
<?php
use Aura\Web\WebFactory;

$web_factory = new WebFactory($GLOBALS);
$request = $web_factory->newResponse();
?>
```

The _Response_ object is composed of several property objects representing
different parts of the response:

- `$response->status` for the status code, status phrase, and HTTP version

- `$response->cookies` for cookie values

- `$response->headers` for non-cookie headers

- `$response->cache` for enabling/disabling HTTP cache headers (these will
  override `$response->headers` values)
  
- `$response->redirect` for setting redirection location and status (these
  will override `$headers` and `$status` values)
  
- `$response->render` to describe how content should be rendered

- `$response->content` for describing the response content, type, charset,
  disposition, and filename (these will override `$headers` values)

The _Response_ object has only one method, `getTransfer()`, which returns a
_StdClass_ object with four properties, one each to describe the status,
the headers, the cookies, and the content.

#### Status

Use the `$response->status` object as follows:

```php
<?php
// set the status code, phrase, and version at once
$response->status->set('404', 'Not Found', '1.1');

// set them individually
$response->status->setCode('404');
$response->status->setPhrase('Not Found');
$response->status->setVersion('1.1');
?>
```

#### Cookies

The `$response->cookies` object has these methods:

- `set()` sets a cookie, and mimics the [setcookie()](http://php.net/setcookie)
  PHP function.

- `get()` returns a cookie by name, or all the cookies in the object.

- `setHttpOnly()` sets the default for whether or not cookies will be sent by
  HTTP only.

#### Headers

The `$response->headers` object has these methods:

- `set()` to set a header label and value

- `add()` to add a value to an existing header

- `get()` to get a single header, or to get all headers

#### Cache

The `$response->cache` object has these methods:

- `setDisabled()` to disable HTTP caching

- `isDisabled()` to say if caching is disabled or not


#### Redirect

The `$response->redirect` object has these methods:

- `to()` a location that the response should redirect to, along with a 
  status code and status phrase.

- `withoutCache()` a location that the response should redirect to, along with a 
  status code and status phrase *and* disables cache.

- `getLocation()` get the redirect location, if any.

- `getStatusCode()` get the status code. See [status code and text](http://en.wikipedia.org/wiki/List_of_HTTP_status_codes)

- `getStatusPhrase()` get the status text. See [status code and text](http://en.wikipedia.org/wiki/List_of_HTTP_status_codes)

- `isWithoutCache()` return true when redirect is set via `withoutCache()`
  else false if set via `to()`

#### Render

The `$response->render` object has these methods:

- `__get` via which you can get `data`, `layout`, `layout_stack`, `view`, `view_stack`

- `__set` via which you can set `data`, `layout`, `layout_stack`, `view`, `view_stack`

#### Content

The `$response->content` object has these methods:

- `set()` set the body content of the response

- `get()` get the body content of the response which has been set via `set()`

- `setCharset()` set the characterset for the response

- `getCharset()` get the characterset for the response set via `setCharset()`

- `setDisposition()` set the content-disposition headers

- `getDisposition()` returns the content-disposition headers set via `setDisposition`

- `getFilename()` get the filename set via `setDisposition`

- `setType()` set the Content-Type of the response.

- `getType()` gets the Content-Type of the response.
