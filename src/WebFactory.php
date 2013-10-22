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

class WebFactory
{
    /**
     * 
     * @var array
     * 
     */
    protected $globals = array();
    
    /**
     * 
     * @var array
     * 
     */
    protected $agents = array(
        'mobile' => array(),
        'crawler' => array(),
    );
    
    /**
     * 
     * @var array
     * 
     */
    protected $decoders = array();
    
    /**
     * 
     * @var array
     * 
     */
    protected $types = array();
    
    /**
     * 
     * @var string
     * 
     */
    protected $method_field;
    
    /**
     * 
     * Constructor
     * 
     * @param array $globals
     * 
     * @param array $mobile_agents
     * 
     * @param array $crawler_agents
     * 
     * @param array $decoders
     * 
     * @param array $types
     * 
     * @param string $method_field
     * 
     */
    public function __construct(
        array $globals,
        array $mobile_agents = array(),
        array $crawler_agents = array(),
        array $decoders = array(),
        array $types = array(),
        $method_field = null
    ) {
        $this->globals = $globals;
        $this->setMobileAgents($mobile_agents);
        $this->setCrawlerAgents($crawler_agents);
        $this->setDecoders($decoders);
        $this->setTypes($types);
        $this->setMethodField($method_field);
    }
    
    /**
     * 
     * Set the mobile agent
     * 
     * @param array $agents
     * 
     */
    public function setMobileAgents(array $agents)
    {
        $this->agents['mobile'] = $agents;
    }
    
    /**
     * 
     * Set the crawler agent
     * 
     * @param array $agents
     * 
     */
    public function setCrawlerAgents(array $agents)
    {
        $this->agents['crawler'] = $agents;
    }
    
    /**
     * 
     * Set the content type decoders
     * 
     * @param array $decoders
     * 
     */
    public function setDecoders(array $decoders)
    {
        $this->decoders = $decoders;
    }
    
    /**
     * 
     * Set the content type
     * 
     * @param array $types
     * 
     */
    public function setTypes(array $types)
    {
        $this->types = $types;
    }
    
    /**
     * 
     * Set the method field
     * 
     * @param string $method_field
     * 
     */
    public function setMethodField($method_field)
    {
        $this->method_field = $method_field;
    }
    
    /**
     * 
     * Return a Request object
     * 
     * @return object Aura\Web\Request
     * 
     */
    public function newRequest()
    {
        return new Request(
            $this->newRequestClient(),
            $this->newRequestContent(),
            $this->newRequestGlobals(),
            $this->newRequestHeaders(),
            $this->newRequestMethod(),
            $this->newRequestNegotiate(),
            $this->newRequestParams(),
            $this->newRequestUrl()
        );
    }
    
    /**
     * 
     * Return a Aura\Web\Request\Client object
     * 
     * @return object Aura\Web\Request\Client
     * 
     */
    public function newRequestClient()
    {
        return new Request\Client(
            $this->get('_SERVER'),
            $this->agents
        );
    }
    
    /**
     * 
     * Return a Aura\Web\Request\Content object
     * 
     * @return object Aura\Web\Request\Content
     * 
     */
    public function newRequestContent()
    {
        return new Request\Content(
            $this->get('_SERVER'),
            $this->decoders
        );
    }
    
    /**
     * 
     * Return a Request Cookies object
     * 
     * @return object Aura\Web\Request\Values
     * 
     */
    public function newRequestCookies()
    {
        return new Request\Values($this->get('_COOKIE'));
    }
    
    /**
     * 
     * Return a Request Environment object
     * 
     * @return object Aura\Web\Request\Values
     * 
     */
    public function newRequestEnv()
    {
        return new Request\Values($this->get('_ENV'));
    }
    
    /**
     * 
     * Return the request files object
     * 
     * @return object Aura\Web\Request\Files
     * 
     */
    public function newRequestFiles()
    {
        return new Request\Files($this->get('_FILES'));
    }
    
    /**
     * 
     * Return an object containing cookies, environment, files, post, query, server object
     * 
     * @return object Aura\Web\Request\Globals
     * 
     */
    public function newRequestGlobals()
    {
        return new Request\Globals(
            $this->newRequestCookies(),
            $this->newRequestEnv(),
            $this->newRequestFiles(),
            $this->newRequestPost(),
            $this->newRequestQuery(),
            $this->newRequestServer()
        );
    }
    
    /**
     * 
     * Return a Headers for a Request
     * 
     * @return object Aura\Web\Request\Headers
     * 
     */
    public function newRequestHeaders()
    {
        return new Request\Headers($this->get('_SERVER'));
    }
    
    /**
     * 
     * Return a Request Method
     * 
     * @return object Aura\Web\Request\Method
     * 
     */
    public function newRequestMethod()
    {
        return new Request\Method(
            $this->get('_SERVER'),
            $this->get('_POST'),
            $this->method_field
        );
    }
    
    /**
     * 
     * Return a Aura\Web\Request\Negotiate object
     * 
     * @return object Aura\Web\Request\Negotiate
     * 
     */
    public function newRequestNegotiate()
    {
        return new Request\Negotiate(
            $this->get('_SERVER'),
            $this->types
        );
    }
    
    /**
     * 
     * Return a Aura\Web\Request\Params object
     * 
     * @return object Aura\Web\Request\Params
     * 
     */
    public function newRequestParams(array $data = array())
    {
        return new Request\Params($data);
    }
    
    /**
     * 
     * Return the $_POST values object
     * 
     * @return object Aura\Web\Request\Values
     * 
     */
    public function newRequestPost()
    {
        return new Request\Values($this->get('_POST'));
    }
    
    /**
     * 
     * Return the $_GET values as an object
     * 
     * @return object Aura\Web\Request\Values
     * 
     */
    public function newRequestQuery()
    {
        return new Request\Values($this->get('_GET'));
    }
    
    /**
     * 
     * Return the server values from the Request
     * 
     * @return object Aura\Web\Request\Values
     * 
     */
    public function newRequestServer()
    {
        return new Request\Values($this->get('_SERVER'));
    }
    
    /**
     * 
     * Return a Aura\Web\Request\Url object
     * 
     * @return object Aura\Web\Request\Url
     * 
     */
    public function newRequestUrl()
    {
        return new Request\Url($this->get('_SERVER'));
    }

    /**
     * 
     * Return a Aura\Web\Response object
     * 
     * @return object Aura\Web\Response
     * 
     */
    public function newResponse()
    {
        $status  = $this->newResponseStatus();
        $headers = $this->newResponseHeaders();
        $cookies = $this->newResponseCookies();
        $content = $this->newResponseContent($headers);
        $render  = $this->newResponseRender();
        $cache   = $this->newResponseCache($headers);
        return new Response(
            $status,
            $headers,
            $cookies,
            $content,
            $render,
            $cache
        );
    }
    
    /**
     * 
     * Return a response object of type Aura\Web\Response\Cache
     * 
     * @return object Aura\Web\Response\Cache
     * 
     */
    public function newResponseCache($headers)
    {
        return new Response\Cache($headers);
    }
    
    /**
     * 
     * Return a response object of type Aura\Web\Response\Content
     * 
     * @return object Aura\Web\Response\Content
     * 
     */
    public function newResponseContent($headers)
    {
        return new Response\Content($headers);
    }
    
    /**
     * 
     * Return a response object of type Aura\Web\Response\Cookies
     * 
     * @return object Aura\Web\Response\Cookies
     * 
     */
    public function newResponseCookies()
    {
        return new Response\Cookies;
    }
    
    /**
     * 
     * Return a renderer object
     * 
     * @return object Aura\Web\Response\Render
     * 
     */
    public function newResponseRender()
    {
        return new Response\Render;
    }
    
    /**
     * 
     * Return a Aura\Web\Response\Headers object
     * 
     * @return object Aura\Web\Response\Headers
     * 
     */
    public function newResponseHeaders()
    {
        return new Response\Headers;
    }
    
    /**
     * 
     * Returns the Response Status
     * 
     * @return object Aura\Web\Response\Status
     * 
     */
    public function newResponseStatus()
    {
        return new Response\Status;
    }
    
    /**
     * 
     * @return array
     * 
     */
    protected function get($key)
    {
        return isset($this->globals[$key])
             ? $this->globals[$key]
             : array();
    }
}
