<?php
namespace Aura\Web;

class WebFactory
{
    protected $globals = array();
    
    protected $agents = array(
        'mobile' => array(),
        'crawler' => array(),
    );
    
    protected $decoders = array();
    
    protected $types = array();
    
    protected $method_field;
    
    public function __construct(array $globals)
    {
        $this->globals = $globals;
    }
    
    public function setMobileAgents(array $agents)
    {
        $this->agents['mobile'] = $agents;
    }
    
    public function setCrawlerAgents(array $agents)
    {
        $this->agents['crawler'] = $agents;
    }
    
    public function setDecoders(array $decoders)
    {
        $this->decoders = $decoders;
    }
    
    public function setTypes(array $types)
    {
        $this->types = $types;
    }
    
    public function setMethodField($method_field)
    {
        $this->method_field = $method_field;
    }
    
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
    
    public function newRequestClient()
    {
        return new Request\Client(
            $this->get('_SERVER'),
            $this->agents
        );
    }
    
    public function newRequestContent()
    {
        return new Request\Content(
            $this->get('_SERVER'),
            $this->decoders
        );
    }
    
    public function newRequestCookies()
    {
        return new Request\Values($this->get('_COOKIE'));
    }
    
    public function newRequestEnv()
    {
        return new Request\Values($this->get('_ENV'));
    }
    
    public function newRequestFiles()
    {
        return new Request\Files($this->get('_FILES'));
    }
    
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
    
    public function newRequestHeaders()
    {
        return new Request\Headers($this->get('_SERVER'));
    }
    
    public function newRequestMethod()
    {
        return new Request\Method(
            $this->get('_SERVER'),
            $this->get('_POST'),
            $this->method_field
        );
    }
    
    public function newRequestNegotiate()
    {
        return new Request\Negotiate(
            $this->get('_SERVER'),
            $this->types
        );
    }
    
    public function newRequestParams(array $data = array())
    {
        return new Request\Params($data);
    }
    
    public function newRequestPost()
    {
        return new Request\Values($this->get('_POST'));
    }
    
    public function newRequestQuery()
    {
        return new Request\Values($this->get('_GET'));
    }
    
    public function newRequestServer()
    {
        return new Request\Values($this->get('_SERVER'));
    }
    
    public function newRequestUrl()
    {
        return new Request\Url($this->get('_SERVER'));
    }

    public function newResponse()
    {
        return new Response(
            $this->newResponseCache(),
            $this->newResponseContent(),
            $this->newResponseCookies(),
            $this->newResponseHeaders(),
            $this->newResponseRedirect(),
            $this->newResponseRender(),
            $this->newResponseStatus()
        );
    }
    
    public function newResponseCache()
    {
        return new Response\Cache;
    }
    
    public function newResponseContent()
    {
        return new Response\Content;
    }
    
    public function newResponseCookies()
    {
        return new Response\Cookies;
    }
    
    public function newResponseRender()
    {
        return new Response\Render;
    }
    
    public function newResponseHeaders()
    {
        return new Response\Headers;
    }
    
    public function newResponseRedirect()
    {
        return new Response\Redirect;
    }

    public function newResponseStatus()
    {
        return new Response\Status;
    }
    
    protected function get($key)
    {
        return isset($this->globals[$key])
             ? $this->globals[$key]
             : array();
    }
}
