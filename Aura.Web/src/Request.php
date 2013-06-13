<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Web
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web;

use Aura\Web\Request\ValueFactory;

/**
 * 
 * Collection point for information about the web environment; this is *not*
 * an HTTP request, it is a representation of data provided by PHP.
 * 
 * @package Aura.Web
 * 
 */
class Request
{
    /**
     * 
     * An object representing client/browser values.
     * 
     * @var Client
     * 
     */
    protected $client;
    
    /**
     * 
     * An object representing the `php://input` value.
     * 
     * @var Content
     * 
     */
    protected $content;

    /**
     * 
     * A superglobal object representing $_COOKIE values.
     * 
     * @var Superglobal
     * 
     */
    protected $cookies;

    /**
     * 
     * A superglobal object representing $_ENV values.
     * 
     * @var Superglobal
     * 
     */
    protected $env;

    /**
     * 
     * A superglobal object representing $_GET values.
     * 
     * @var Files
     * 
     */
    protected $files;

    /**
     * 
     * An object representing $_SERVER['HTTP_*'] header values.
     * 
     * @var Headers
     * 
     */
    protected $headers;

    /**
     * 
     * An object representing the HTTP method.
     * 
     * @var Method
     * 
     */
    protected $method;
    
    /**
     * 
     * An object representing negotiable "accept" values.
     * 
     * @var Negotiate
     * 
     */
    protected $negotiate;
    
    /**
     * 
     * A superglobal object representing $_POST values.
     * 
     * @var Superglobal
     * 
     */
    protected $post;

    /**
     * 
     * A superglobal object representing $_GET values.
     * 
     * @var Superglobal
     * 
     */
    protected $query;

    /**
     * 
     * A superglobal object representing $_SERVER values.
     * 
     * @var Superglobal
     * 
     */
    protected $server;

    /**
     * 
     * Constructor.
     * 
     * @param ValueFactory $value_factory A factory to create value objects.
     * 
     */
    public function __construct(ValueFactory $value_factory)
    {
        $this->cookies   = $value_factory->newCookies();
        $this->env       = $value_factory->newEnv();
        $this->files     = $value_factory->newFiles();
        $this->headers   = $value_factory->newHeaders();
        $this->content   = $value_factory->getContent();
        $this->method    = $value_factory->newMethod();
        $this->negotiate = $value_factory->newNegotiate();
        $this->post      = $value_factory->newPost();
        $this->query     = $value_factory->newQuery();
        $this->server    = $value_factory->newServer();
    }

    /**
     * 
     * Read-only access to value objects.
     * 
     * @param string $key The value object get.
     * 
     * @return mixed The value object.
     * 
     */
    public function __get($key)
    {
        return $this->$key;
    }
}
