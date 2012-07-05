Aura Web
========

The Aura Web package provides tools to build web page controllers, including
an `AbstractPage` for action methods, a `Context` class for discovering the
request environment, and a `Response` transfer object that describes the
eventual HTTP response. (Note that the `Response` transfer object is not
itself an HTTP response.)

The Aura Web package has no dependencies, and does not impose any particular
routing or rendering system on the developer.


Getting Started
===============

Instantiation
-------------

Most Aura packages allow you to instantiate an object by including a
particular file. This is not the case with Aura Web. Because page controllers
are so specific to the logic of your particular needs, you will have to extend
the `AbstractPage` class yourself and add action methods for your own
purposes.

First, either include the the `Aura.Web/src.php` file to load the package
classes, or add the `Aura.Web/src/` directory to your autoloader.

Next, create a page controller class of your own, extending the `AbstractPage`
class:

    <?php
    namespace Vendor\Package\Web;
    use Aura\Web\AbstractPage;
    class Page extends AbstractPage
    {
        
    }

To instantiate the page controller class, you will need to pass it a `Context`
and a `Response` transfer object as dependencies.

    <?php
    use Vendor\Package\Web\Page;
    use Aura\Web\Context;
    use Aura\Web\Response;
    use Aura\Web\Renderer\None as Renderer;
    $page = new Page(new Context($GLOBALS), new Response, new Renderer);
    
If you have a dependency injection mechanism, you can automate the the creation and injection of the dependency objects.  The [Aura.Di][] package is one such system.


The Execution Cycle
-------------------

The heart of the page controller is its execution cycle. You invoke the page
controller by calling `exec()` and passing it an array of parameters. These
will determine what action method is called, what the parameters for that
method will be, and what rendering format is expected. The return value is a
`Response` transfer object describing how to build your HTTP response.

    <?php
    use Vendor\Package\Web\Page;
    use Aura\Web\Context;
    use Aura\Web\Response;
    
    $params = [
        'action' => 'hello',
        'format' => '.html',
        'noun'   => 'world',
    ];
    
    $page = new Page(new Context, new Response, $params);
    
    $response = $page->exec();

The parameters are generally retrieved from a routing mechanism of some sort, such as the one provided by the [Aura.Router][] package.

Internally, the `exec()` cycle runs ...

- A `preExec()` hook to let you set up the object,
- A `preAction()` hook to prepare for the action,
- The `action()` method to invoke the method determined by the `'action'` param value
- A `postAction()` hook,
- A `preRender()` hook to prepare for rendering,
- The `render()` method to render a presentation (this is up to the developer to create),
- A `postRender()` hook, and
- A `postExec()` hook.

At the end of this, the `exec()` method returns a `Response` transfer object.  Note that the `Response` object is not an HTTP response proper; it is a data transfer object that has information on how to build an HTTP response.  You would need to inspect the `Response` object and use that information to build an HTTP response of your own.  (The [Aura.Http][] package provides an HTTP response object proper.)


Action Methods
--------------

At this point, calling `exec()` on the page controller will do nothing,
because there are no corresponding action methods. To add an action method to
the page controller, create it as a method named `action*()` with any
parameters it needs:

    <?php
    namespace Vendor\Package\Web;
    use Aura\Web\AbstractPage;
    class Page extends AbstractPage
    {
        public function actionHello($noun = null)
        {
            $noun = htmlspecialchars($noun, ENT_QUOTES, 'UTF-8');
            $content = "Hello, {$noun}!";
            $this->response->setContent($content);
        }
    }
    
Now when you call `$page->exec()` as above, you will find that the `Response`
transfer object has some content in it.

    <?php
    use Vendor\Package\Web\Page;
    use Aura\Web\Context;
    use Aura\Web\Response;
    
    $params = [
        'action' => 'hello',
        'format' => '.html',
        'noun'   => 'world',
    ];
    
    $page = new Page(new Context($GLOBALS), new Response, $params);
    
    $response = $page->exec();
    echo $response->getContent(); // "Hello, world!"


The Response Transfer Object
----------------------------

To manipulate the response description, use the `$this->response` transfer
object. Some of the important methods are:

- `setContent()`: sets the body content
- `setHeader()`: sets a single header value
- `setCookie()`: sets a single cookie
- `setRedirect()`: sets a `Location:` header for redirect, with an optional status code and message (default is `'302 Found'`.)
- `setStatusCode()` and `setStatusText()`: sets the HTTP status code and message

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
- `getAccept()`: gets the Accept headers, ordered by weight
- `isGet()`, `isPut()`, `isXhr()`, etc.: Tells if the request method was `GET`, `PUT`, an `Xml-HTTP-Request`, etc.

For more information, please review the [Context][] class.

An example "search" action using a "terms" query string parameter might look
like this:

    <?php
    public function actionSearch()
    {
        $terms = $this->context->getQuery('terms');
        if ($terms) {
            // ... now search a database ...
        }
    }

Given a URI with the query string `'?terms=foo+bar+baz'`, the `$terms`
variable would be `'foo bar baz'`. If there was no `'terms'` item in the query
string, `$terms` would be null.


Data and Rendering
------------------

Usually, you will not want to manipulate the `Response` content directly in
the action method. It is almost always the case that you will collect data
inside the action method, then hand off to a rendering system to present that
data.

The `AbstractPage` provides a `$data` property and a `render()` method for
just that purpose. Here is a naive example of how to use them:

    <?php
    namespace Vendor\Package\Web;
    use Aura\Web\AbstractPage;
    class Page extends AbstractPage
    {
        public function actionHello($noun = null)
        {
            $this->data->noun = $noun;
        }
        
        public function render()
        {
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
                $this->response->setStatusCode('404');
                $success = false;
                $content = 'Action not found.';
                break;
            }
            
            // convert to a JSON response?
            if ($this->getFormat() == '.json') {
                $this->response->setContentType('application/json');
                $content = json_encode([
                    'success' => $success,
                    'content' => $content,
                ]);
            }
            
            $this->response->setContent($content);
        }
    }

The `render()` method is empty by default. This allows you to add in whatever
presentation logic you want, from simply `json_encode()`-ing `$this->data`, to
using a complex two-step or transform view. The [Aura.View][] package provides
a powerful view system suitable for use here.

* * *

[Aura.Di]:      https://github.com/auraphp/Aura.Di
[Aura.Router]:  https://github.com/auraphp/Aura.Router 
[Aura.Http]:    https://github.com/auraphp/Aura.Http 
[Aura.View]:    https://github.com/auraphp/Aura.View 
[Response]:     https://github.com/auraphp/Aura.Web/blob/master/src/Aura/Web/Response.php
[Context]:      https://github.com/auraphp/Aura.Web/blob/master/src/Aura/Web/Context.php