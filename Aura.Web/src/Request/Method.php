<?php
namespace Aura\Web\Request;

use BadMethodCallException;

class Method
{
    protected $value;
    
    public function __construct($server)
    {
        // set the original value
        $this->value = strtoupper($server['REQUEST_METHOD']);
        
        // must be a POST to do an override
        if ($this->value == 'POST') {
            // look for override in headers
            $override = $server['HTTP_X_HTTP_METHOD_OVERRIDE'];
            if ($override) {
                $this->value = strtoupper($override);
            }
        }
    }
    
    // allow for new methods
    public function __call($method, $params)
    {
        if (substr($method, 0, 2) == 'is') {
            return $this->value == strtoupper(substr($method, 2));
        }
        
        throw new BadMethodCallException($method);
    }
    
    public function get()
    {
        return $this->value;
    }    
    
    public function isDelete()
    {
        return $this->value == 'DELETE';
    }
    
    public function isGet()
    {
        return $this->value == 'GET';
    }
    
    public function isHead()
    {
        return $this->value == 'HEAD';
    }
    
    public function isOptions()
    {
        return $this->value == 'OPTIONS';
    }
    
    public function isPatch()
    {
        return $this->value == 'PATCH';
    }
    
    public function isPut()
    {
        return $this->value == 'PUT';
    }
    
    public function isPost()
    {
        return $this->value == 'POST';
    }

    public function isTrace()
    {
        return $this->value == 'TRACE';
    }
}
