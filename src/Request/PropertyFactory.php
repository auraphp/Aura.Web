<?php
namespace Aura\Web\Request;

class PropertyFactory
{
    protected $globals;
    
    protected $agents;
    
    protected $decoders;
    
    protected $types;
    
    protected $method_field;
    
    public function __construct(
        array $globals,
        array $agents = array(),
        array $decoders = array(),
        array $types = array(),
        $method_field = null
    ) {
        $this->globals      = $this->sanitize($globals);
        $this->agents       = $agents;
        $this->decoders     = $decoders;
        $this->types        = $types;
        $this->method_field = $method_field;
    }
    
    public function newClient()
    {
        return new Client(
            $this->get('_SERVER'),
            $this->agents
        );
    }
    
    public function newContent()
    {
        return new Content(
            $this->get('_SERVER'),
            $this->decoders
        );
    }
    
    public function newCookies()
    {
        return new Values($this->get('_COOKIE'));
    }
    
    public function newEnv()
    {
        return new Values($this->get('_ENV'));
    }
    
    public function newFiles()
    {
        return new Files($this->get('_FILES'));
    }
    
    public function newHeaders()
    {
        return new Headers($this->get('_SERVER'));
    }
    
    public function newMethod()
    {
        return new Method(
            $this->get('_SERVER'),
            $this->get('_POST'),
            $this->method_field
        );
    }
    
    public function newNegotiate()
    {
        return new Negotiate(
            $this->get('_SERVER'),
            $this->types
        );
    }
    
    public function newParams(array $data = array())
    {
        return new Params($data);
    }
    
    public function newPost()
    {
        return new Values($this->get('_POST'));
    }
    
    public function newQuery()
    {
        return new Values($this->get('_GET'));
    }
    
    public function newServer()
    {
        return new Values($this->get('_SERVER'));
    }
    
    protected function get($key)
    {
        return isset($this->globals[$key])
             ? $this->globals[$key]
             : array();
    }
    
    protected function sanitize($globals)
    {
        // sanitize the $_SERVER['HTTP_*'] values here because they get used
        // in so many places. this strips control characters, including tabs,
        // newlines, and linefeeds.
        foreach ($globals['_SERVER'] as $key => $val) {
            if (substr($key, 0, 5) == 'HTTP_') {
                // remove the existing key
                unset($globals['_SERVER'][$key]);
                // sanitize the new label and value
                $label = preg_replace('/[\x00-\x1F]/', '', $key);
                $value = preg_replace('/[\x00-\x1F]/', '', $val);
                // retain the new label and value
                $globals['_SERVER'][$label] = $value;
            }
        }
        
        // further sanitize headers to remove HTTP_X_JSON headers
        unset($globals['_SERVER']['HTTP_X_JSON']);
        return $globals;
    }
}
