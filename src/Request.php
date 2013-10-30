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
     * @var Accept
     * 
     */
    protected $accept;
    
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
     * An object representing the URL.
     * 
     * @var Url
     * 
     */
    protected $url;

    /**
     * 
     * Is this an XML HTTP request?
     * 
     * @var bool
     * 
     */
    protected $xhr = false;
    
    /**
     * 
     * Constructor.
     * 
     * @param PropertyFactory $property_factory A factory to create property
     * objects.
     * 
     */
    public function __construct(
        Request\Client  $client,
        Request\Content $content,
        Request\Globals $globals,
        Request\Headers $headers,
        Request\Method  $method,
        Request\Accept  $accept,
        Request\Params  $params,
        Request\Url     $url
    ) {
        $this->client  = $client;
        $this->content = $content;
        $this->cookies = $globals->cookies;
        $this->env     = $globals->env;
        $this->files   = $globals->files;
        $this->headers = $headers;
        $this->method  = $method;
        $this->accept  = $accept;
        $this->params  = $params;
        $this->post    = $globals->post;
        $this->query   = $globals->query;
        $this->server  = $globals->server;
        $this->url     = $url;

        $with = strtolower($this->server->get('HTTP_X_REQUESTED_WITH'));
        if ($with == 'xmlhttprequest') {
            $this->xhr = true;
        }
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
    
    /**
     * 
     * Is this an XML HTTP request?
     * 
     * @return bool
     * 
     */
    public function isXhr()
    {
        return $this->xhr;
    }
}
