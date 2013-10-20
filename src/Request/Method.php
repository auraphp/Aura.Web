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
namespace Aura\Web\Request;

use BadMethodCallException;

class Method
{
    /**
     * 
     * the request method value
     * 
     * @var string
     * 
     */
    protected $value;

    /**
     * 
     * Constructor
     * 
     * @param array $server server value
     * 
     * @param array $post An array of post values
     * 
     * @param string $method_field Special field to indicate a custom HTTP method
     * 
     */
    public function __construct(
        array $server,
        array $post,
        $method_field = null
    ) {
        // set the original value
        if (isset($server['REQUEST_METHOD'])) {
            $this->value = strtoupper($server['REQUEST_METHOD']);
        }
        
        // must be a POST to do an override
        if ($this->value == 'POST') {
            
            // look for this method field in the post data
            if (! $method_field) {
                $method_field = '_method';
            }
            
            // look for override in post data
            $override = isset($post[$method_field])
                      ? $post[$method_field]
                      : false;
            if ($override) {
                $this->value = strtoupper($override);
            }
            
            // look for override in headers
            $override = isset($server['HTTP_X_HTTP_METHOD_OVERRIDE'])
                      ? $server['HTTP_X_HTTP_METHOD_OVERRIDE']
                      : false;
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
    
    /**
     * 
     * Returns the request method value
     * 
     * @return string request method value
     * 
     */
    public function get()
    {
        return $this->value;
    }    
    
    /**
     * 
     * Did the request use a DELETE method?
     * 
     * @return bool True|False
     * 
     */
    public function isDelete()
    {
        return $this->value == 'DELETE';
    }
    
    /**
     * 
     * Did the request use a GET method?
     * 
     * @return bool True|False
     * 
     */
    public function isGet()
    {
        return $this->value == 'GET';
    }
    
    /**
     * 
     * Did the request use a HEAD method?
     * 
     * @return bool True|False
     * 
     */
    public function isHead()
    {
        return $this->value == 'HEAD';
    }
    
    /**
     * 
     * Did the request use an OPTIONS method?
     * 
     * @return bool True|False
     * 
     */
    public function isOptions()
    {
        return $this->value == 'OPTIONS';
    }
    
    /**
     * 
     * Did the request use a PATCH method?
     * 
     * @return bool True|False
     * 
     */
    public function isPatch()
    {
        return $this->value == 'PATCH';
    }
    
    /**
     * 
     * Did the request use a PUT method?
     * 
     * @return bool True|False
     * 
     */
    public function isPut()
    {
        return $this->value == 'PUT';
    }
    
    /**
     * 
     * Did the request use a POST method?
     * 
     * @return bool True|False
     * 
     */
    public function isPost()
    {
        return $this->value == 'POST';
    }
}
