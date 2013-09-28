<?php
/**
 * 
 * This file is part of Aura for PHP.
 * 
 * @package Aura.Web
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web;

use Aura\Web\Request\PropertyFactory;

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
     * An object representing $_COOKIE values.
     * 
     * @var Values
     * 
     */
    protected $cookies;

    /**
     * 
     * An object representing $_ENV values.
     * 
     * @var Values
     * 
     */
    protected $env;

    /**
     * 
     * An object representing $_FILES values.
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
     * An object representing arbitrary parameter values; e.g., from a router.
     * 
     * @var Params
     * 
     */
    protected $params;
    
    /**
     * 
     * An object representing $_POST values.
     * 
     * @var Values
     * 
     */
    protected $post;

    /**
     * 
     * An object representing $_GET values.
     * 
     * @var Values
     * 
     */
    protected $query;

    /**
     * 
     * An object representing $_SERVER values.
     * 
     * @var Values
     * 
     */
    protected $server;

    /**
     * 
     * Constructor.
     * 
     * @param PropertyFactory $property_factory A factory to create property
     * objects.
     * 
     */
    public function __construct(PropertyFactory $property_factory)
    {
        $this->client    = $property_factory->newClient();
        $this->cookies   = $property_factory->newCookies();
        $this->env       = $property_factory->newEnv();
        $this->files     = $property_factory->newFiles();
        $this->headers   = $property_factory->newHeaders();
        $this->content   = $property_factory->newContent();
        $this->method    = $property_factory->newMethod();
        $this->negotiate = $property_factory->newNegotiate();
        $this->params    = $property_factory->newParams();
        $this->post      = $property_factory->newPost();
        $this->query     = $property_factory->newQuery();
        $this->server    = $property_factory->newServer();
    }

    /**
     * 
     * Read-only access to property objects.
     * 
     * @param string $key The name of the property object to read.
     * 
     * @return mixed The property object.
     * 
     */
    public function __get($key)
    {
        return $this->$key;
    }
}
