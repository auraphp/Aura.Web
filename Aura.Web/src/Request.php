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

use Aura\Web\Request\Factory;

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
     * An object representing client/browser information.
     * 
     * @var Client
     * 
     */
    protected $client;
    
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
     * The value of `php://input`.
     * 
     * @var string
     * 
     */
    protected $input;

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
     * @param Factory $factory A factory to create value objects.
     * 
     */
    public function __construct(Factory $factory)
    {
        $this->cookies = $factory->newCookies();
        $this->env     = $factory->newEnv();
        $this->files   = $factory->newFiles();
        $this->headers = $factory->newHeaders();
        $this->input   = $factory->getContent();
        $this->method  = $factory->newMethod();
        $this->post    = $factory->newPost();
        $this->query   = $factory->newQuery();
        $this->server  = $factory->newServer();
    }

    /**
     * 
     * Read-only access to properties.
     * 
     * @param string $key The property to read.
     * 
     * @return mixed The property value.
     * 
     */
    public function __get($key)
    {
        return $this->$key;
    }
}
