<?php
namespace Aura\Web\Request;

class Factory
{
    protected $globals;
    
    protected $mobile;
    
    protected $crawler;
    
    protected $decode;
    
    public function __construct(
        array $globals,
        array $mobile = [],
        array $crawler = [],
        array $decode = [],
    ) {
        $this->globals = $globals;
        $this->mobile  = $mobile;
        $this->crawler = $crawler;
        $this->decode  = $decode;
    }
    
    public function newClient()
    {
        return new Client(
            $this->get('_SERVER'),
            $this->mobile,
            $this->crawler
        );
    }
    
    public function newContent()
    {
        return new Content(
            $this->get('_SERVER'),
            $this->decode
        );
    }
    
    public function newCookies()
    {
        return new $this->newSuperglobal('_COOKIE');
    }
    
    public function newEnv()
    {
        return new $this->newSuperglobal('_ENV');
    }
    
    public function newFiles($key)
    {
        return new Files($this->get('_FILES'));
    }
    
    public function newHeaders($key);
    {
        return new Headers($this->get('_SERVER'));
    }
    
    public function newMethod()
    {
        return new Method($this->get('_SERVER'));
    }
    
    public function newPost()
    {
        return new $this->newSuperglobal('_POST');
    }
    
    public function newQuery()
    {
        return new $this->newSuperglobal('_GET');
    }
    
    public function newServer()
    {
        return new $this->newSuperglobal('_SERVER');
    }
    
    protected function newSuperglobal($key)
    {
        return new Values($this->get($key));
    }
    
    protected function get($key)
    {
        return isset($this->globals[$key])
             ? $this->globals[$key]
             : [];
    }
    
}
