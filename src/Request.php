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
     * @var Request\Client
     *
     */
    protected $client;

    /**
     *
     * An object representing the `php://input` value.
     *
     * @var Request\Content
     *
     */
    protected $content;

    /**
     *
     * An object representing $_COOKIE values.
     *
     * @var Request\Values
     *
     */
    protected $cookies;

    /**
     *
     * An object representing $_ENV values.
     *
     * @var Request\Values
     *
     */
    protected $env;

    /**
     *
     * An object representing $_FILES values.
     *
     * @var Request\Files
     *
     */
    protected $files;

    /**
     *
     * An object representing $_SERVER['HTTP_*'] header values.
     *
     * @var Request\Headers
     *
     */
    protected $headers;

    /**
     *
     * An object representing the HTTP method.
     *
     * @var Request\Method
     *
     */
    protected $method;

    /**
     *
     * An object representing negotiable "accept" values.
     *
     * @var Request\Accept
     *
     */
    protected $accept;

    /**
     *
     * An object representing arbitrary parameter values; e.g., from a router.
     *
     * @var Request\Params
     *
     */
    protected $params;

    /**
     *
     * An object representing $_POST values.
     *
     * @var Request\Values
     *
     */
    protected $post;

    /**
     *
     * An object representing $_GET values.
     *
     * @var Request\Values
     *
     */
    protected $query;

    /**
     *
     * An object representing $_SERVER values.
     *
     * @var Request\Values
     *
     */
    protected $server;

    /**
     *
     * An object representing the URL.
     *
     * @var Request\Url
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
	 * @param Request\Client  $client  A client object.
	 *
	 * @param Request\Content $content A content object.
	 *
	 * @param Request\Globals $globals A globals object.
	 *
	 * @param Request\Headers $headers A headers object.
	 *
	 * @param Request\Method  $method  A method object.
	 *
	 * @param Request\Accept  $accept  An accept object.
	 *
	 * @param Request\Params  $params  A params object.
	 *
	 * @param Request\Url     $url     A url object.
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
