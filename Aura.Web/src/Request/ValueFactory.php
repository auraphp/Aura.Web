<?php
namespace Aura\Web\Request;

/**
 * Need to be sure we are sanitizing values when appropriate,
 * e.g. $_SERVER['HTTP_*'] values.
 */
class ValueFactory
{
    protected $globals;
    
    protected $agents;
    
    protected $decoders;
    
    protected $types;
    
    public function __construct(
        array $globals,
        array $agents = [],
        $stream = null,
        array $decoders = [],
        array $types = [],
        $method_field = null
    ) {
        $this->globals      = $this->sanitize($globals);
        $this->agents       = $mobile;
        $this->stream       = $stream ? $stream : 'php://input';
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
            $this->stream,
            $this->decoders
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
    
    public function newHeaders($key)
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
    
    protected function sanitize($globals)
    {
        if (! $globals['_SERVER']) {
            return;
        }
        
        // sanitize the $_SERVER['HTTP_*'] values here because they get used
        // in so many places. this strips control characters, including tabs,
        // newlines, and linefeeds.
        foreach ($globals['_SERVER'] as $key => $val) {
            if (substr($key, 0, 5) == 'HTTP_') {
                // remove the existing key
                unset($globals['_SERVER'][$key]);
                // sanitize the new label and value
                $label = preg_replace('/[\x00-\x1F]/', '', $label);
                $value = preg_replace('/[\x00-\x1F]/', '', $value);
                // retain the new label and value
                $globals['_SERVER'][$label] = $value;
            }
        }
        
        // further sanitize headers to remove HTTP_X_JSON headers
        unset($globals['_SERVER']['HTTP_X_JSON']);
    }
}
