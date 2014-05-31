Aura Web
========

[![Build Status](https://travis-ci.org/auraphp/Aura.Web.png?branch=develop)](https://travis-ci.org/auraphp/Aura.Web)

The Aura Web package provides tools to build web page controllers, including
an `AbstractPage` for action methods, a `Context` class for discovering the
request environment, and a `Response` transfer object that describes the
eventual HTTP response. (Note that the `Response` transfer object is not
itself an HTTP response.) It also includes a `Signal` interface to handle
calls to controller hooks, as well as a `Renderer` interface to allow for
different rendering strategies.

The Aura Web package has no dependencies, and does not impose any particular
routing, signalling, or rendering system on the developer.

This package is compliant with [PSR-0][], [PSR-1][], and [PSR-2][]. If you
notice compliance oversights, please send a patch via pull request.

[PSR-0]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md


Getting Started
===============

Instantiation
-------------

Most Aura packages allow you to instantiate an object by including a
particular file. This is not the case with `Aura.Web`. Because page
controllers are so specific to the logic of your particular needs, you will
have to extend the `AbstractPage` class yourself and add action methods for
your own purposes.

First, either include the the `Aura.Web/src.php` file to load the package
classes, or add the `Aura.Web/src/` directory to your autoloader.

Next, create a page controller class of your own, extending the `AbstractPage`
class:

```php
<?php
namespace Vendor\Package\Web;

use Aura\Web\Controller\AbstractPage;

class Page extends AbstractPage
{
    
}
```

To instantiate the page controller class, you will need to pass it some
dependency objects:

- a `Context` to represent the execution environment,

- a `Response` transfer object to return the results,

- a `Signal` manager to handle execution hooks, and

- a `Renderer` strategy (the default is "none")

The code would look like this:

```php
<?php
use Vendor\Package\Web\Page;
use Aura\Web\Context;
use Aura\Web\Accept;
use Aura\Web\Response;
use Aura\Web\Signal;
use Aura\Web\Renderer\None as Renderer;

$page = new Page(
    new Context($GLOBALS),
    new Accept($_SERVER),
    new Response,
    new Signal,
    new Renderer
);
```
    
If you have a dependency injection mechanism, you can automate the the
creation and injection of the dependency objects. The [Aura.Di][] package is
one such system.


The Execution Cycle
-------------------

The heart of the page controller is its execution cycle. You invoke the page
controller by calling `exec()` and passing it an array of parameters. These
will determine what action method is called, what the parameters for that
method will be, and what rendering format is expected. The return value is a
`Response` transfer object describing how to build your HTTP response.

```php
<?php
use Vendor\Package\Web\Page;
use Aura\Web\Context;
use Aura\Web\Accept;
use Aura\Web\Response;
use Aura\Web\Signal;
use Aura\Web\Renderer\None as Renderer;

$params = [
    'action' => 'hello',
    'format' => '.html',
    'noun'   => 'world',
];

$page = new Page(
    new Context($GLOBALS),
    new Accept($_SERVER),
    new Response,
    new Signal,
    new Renderer,
    $params
);

$response = $page->exec();
```

The parameters are generally retrieved from a routing mechanism of some sort,
such as the one provided by the [Aura.Router][] package.

The `exec()` cycle runs ...

- the `preExec()` hook to prepare for overall execution,

- the `preAction()` hook to prepare for the action,

- the `action()` method to invoke the method determined by the `'action'`
  param value

- the `postAction()` hook,

- the `preRender()` hook to prepare for rendering,

- the `render()` method to render a presentation (this is up to the developer
  to create),

- the `postRender()` hook, and

- the `postExec()` hook to do work after overall execution.

At the end of this, the `exec()` method returns a `Response` transfer object.
Note that the `Response` object is not an HTTP response proper; it is a data
transfer object that has information on how to build an HTTP response. You
would need to inspect the `Response` object and use that information to build
an HTTP response of your own. (The [Aura.Http][] package provides an HTTP
response object proper.)


Action Methods
--------------

At this point, calling `exec()` on the page controller will do nothing,
because there are no corresponding action methods. To add an action method to
the page controller, create it as a method named `action*()` with any
parameters it needs:

```php
<?php
namespace Vendor\Package\Web;

use Aura\Web\Controller\AbstractPage;

class Page extends AbstractPage
{
    public function actionHello($noun = null)
    {
        $noun = htmlspecialchars($noun, ENT_QUOTES, 'UTF-8');
        $content = "Hello, {$noun}!";
        $this->response->setContent($content);
    }
}
```
    
Now when you call `$page->exec()` as above, you will find that the `Response`
transfer object has some content in it.

```php
<?php
use Vendor\Package\Web\Page;
use Aura\Web\Context;
use Aura\Web\Accept;
use Aura\Web\Response;
use Aura\Web\Signal;
use Aura\Web\Renderer\None as Renderer;

$params = [
    'action' => 'hello',
    'format' => '.html',
    'noun'   => 'world',
];

$page = new Page(
    new Context($GLOBALS),
    new Accept($_SERVER),
    new Response,
    new Signal,
    new Renderer,
    $params
);

$response = $page->exec();
echo $response->getContent(); // "Hello, world!"
```


The Response Transfer Object
----------------------------

To manipulate the response description, use the `$this->response` transfer
object. Some of the important methods are:

- `setContent()`: sets the body content

- `setHeader()`: sets a single header value

- `setCookie()`: sets a single cookie

- `setRedirect()`: sets a `Location:` header for redirect, with an optional
  status code and message (default is `'302 Found'`.)

- `setStatusCode()` and `setStatusText()`: sets the HTTP status code and
  message

For more information, please review the [Response][] class.


The Context Object
------------------

You can discover the web request environment using the `$this->context`
object. Some of the important methods are:

- `getQuery()`: gets a $_GET value

- `getPost()`: gets a $_POST value

- `getFiles()`: gets a $_FILES value

- `getInput()`: gets the raw `php://input` value

- `getJsonInput()`: gets the raw `php://input` value and `json_decode()` it

- `isGet()`, `isPut()`, `isXhr()`, etc.: Tells if the request method was
  `GET`, `PUT`, an `Xml-HTTP-Request`, etc.

For more information, please review the [Context][] class.

An example "search" action using a "terms" query string parameter might look
like this:

```php
<?php
public function actionSearch()
{
    $terms = $this->context->getQuery('terms');
    if ($terms) {
        // ... now search a database ...
    }
}
```

Given a URI with the query string `'?terms=foo+bar+baz'`, the `$terms`
variable would be `'foo bar baz'`. If there was no `'terms'` item in the query
string, `$terms` would be null.


The Accept Object
-----------------

You can discover what the client will accept using the `$this->accept` object.

- `getContentType()`: returns the accepted media types

- `getCharset()`: returns the accepted character sets

- `getEncoding()`: returns the accepted encodings

- `getLanguage()`: returns the accepted languages


Data and Rendering
------------------

Usually, you will not want to manipulate the `Response` content directly in
the action method. It is almost always the case that you will collect data
inside the action method, then hand off to a rendering system to present that
data. The `AbstractPage` provides a `$data` property and a `Renderer` strategy
system for just that purpose.

Here is a naive example of how to use the `$data` property:

```php
<?php
namespace Vendor\Package\Web;

use Aura\Web\Controller\AbstractPage;

class Page extends AbstractPage
{
    public function actionHello($noun = null)
    {
        $this->data->noun = $noun;
    }
}
```

To render the data into the response, you can override the `render()` method
...
    
```php
<?php
public function render()
{
    // get the response object
    $response = $this->getResponse();

    // escape all data
    $data = [];
    foreach ((array) $this->data as $key => $val) {
        $data[$key] = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
    }

    // switch between actions
    switch ($this->getAction()) {
        case 'hello':
            $success = true;
            $content = "Hello, {$data['noun']}!";
            break;
        default:
            $response->setStatusCode('404');
            $success = false;
            $content = 'Action not found.';
            break;
    }

    // convert to a JSON response?
    if ($this->getFormat() == '.json') {
        $response->setContentType('application/json');
        $content = json_encode([
            'success' => $success,
            'content' => $content,
        ]);
    }

    $response->setContent($content);
}
```
    
... or you can create a `Renderer` strategy of your own. (This is the
preferred approach.)

To create a `Renderer` strategy, extend from `AbstractRenderer`, then use the
provided `$controller` property to inspect the controller and render its data
into the response. The following example is identical in effect to the above
`render()` method override, except that it uses `$this->controller` instead of
`$this`.

```php
<?php
namespace Vendor\Package\Web\Renderer;

use Aura\Web\Renderer\AbstractRenderer;

class Naive extends AbstractRenderer
{
    public function exec()
    {
        // get the response object
        $response = $this->controller->getResponse();
        
        // escape all data
        $data = [];
        foreach ((array) $this->controller->getData() as $key => $val) {
            $data[$key] = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
        }
    
        // switch between actions
        switch ($this->controller->getAction()) {
            case 'hello':
                $success = true;
                $content = "Hello, {$data['noun']}!";
                break;
            default:
                $response->setStatusCode('404');
                $success = false;
                $content = 'Action not found.';
                break;
        }
    
        // convert to a JSON response?
        if ($this->controller->getFormat() == '.json') {
            $response->setContentType('application/json');
            $content = json_encode([
                'success' => $success,
                'content' => $content,
            ]);
        }
    
        $response->setContent($content);
    }
}
```

You can then pass the naive renderer strategy to the page controller
constructor, and it will be used automatically at `render()` time:

```php
<?php
use Vendor\Package\Web\Page;
use Aura\Web\Context;
use Aura\Web\Response;
use Aura\Web\Signal;
use Vendor\Package\Web\Renderer\Naive as NaiveRenderer; // <-- strategy

$params = [
    'action' => 'hello',
    'format' => '.html',
    'noun'   => 'world',
];

$page = new Page(
    new Context($GLOBALS),
    new Accept($_SERVER),
    new Response,
    new Signal,
    new NaiveRenderer, // <-- strategy
    $params
);

$response = $page->exec();
echo $response->getContent(); // "Hello, world!"
```

You could write a `Renderer` strategy that uses [Aura.View][], [Mustache][],
or some other templating or view system.


Signal Interface
----------------

The `Aura.Web` package comes with a signal slots interface and a stub signal
manager implementation. These are fine for standalone use, but really they are
provided so that you can implement the interface in your own signal slots (or
observer/listener/notification) system.  One such signal slots approach is
the [Aura.Signal][] package.


* * *

[Aura.Di]:      https://github.com/auraphp/Aura.Di
[Aura.Http]:    https://github.com/auraphp/Aura.Http 
[Aura.Router]:  https://github.com/auraphp/Aura.Router 
[Aura.Signal]:  https://github.com/auraphp/Aura.Signal 
[Aura.View]:    https://github.com/auraphp/Aura.View 
[Context]:      https://github.com/auraphp/Aura.Web/blob/master/src/Aura/Web/Context.php
[Mustache]:     https://github.com/weierophinney/phly_mustache
[Response]:     https://github.com/auraphp/Aura.Web/blob/master/src/Aura/Web/Response.php
