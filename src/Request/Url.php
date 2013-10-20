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

use Aura\Web\Exception;

/**
 * 
 * A Url object from the server values passed
 * 
 * @package Aura.Web
 * 
 */
class Url
{
    /**
     * 
     * @var string Url string
     * 
     */
    protected $string;
    
    /**
     * 
     * @var array parts of the URL
     * 
     */
    protected $parts;
    
    /**
     * 
     * @var bool Indicate whether the request is secure or not
     * 
     */
    protected $secure;
    
    /**
     * 
     * @var array component constants, see http://php.net/parse-url
     * 
     */
    protected $keys = array(
        PHP_URL_SCHEME      => 'scheme',
        PHP_URL_HOST        => 'host',
        PHP_URL_PORT        => 'port',
        PHP_URL_USER        => 'user',
        PHP_URL_PASS        => 'pass',
        PHP_URL_PATH        => 'path',
        PHP_URL_QUERY       => 'query',
        PHP_URL_FRAGMENT    => 'fragment',
    );
    
    /**
     * 
     * Constructor
     * 
     * @param array An array of server values
     * 
     */
    public function __construct(array $server)
    {
        // explicit https scheme?
        $https = isset($server['HTTPS'])
               ? (strtolower($server['HTTPS']) == 'on')
               : false;
        
        // explicit secure port?
        $port  = isset($server['SERVER_PORT'])
               ? ($server['SERVER_PORT'] == 443)
               : false;
        
        // forwarded from an https scheme?
        $fwd   = isset($server['HTTP_X_FORWARDED_PROTO'])
               ? (strtolower($server['HTTP_X_FORWARDED_PROTO']) == 'https')
               : false;
               
        // is this a secure request?
        $this->secure = ($https || $port || $fwd);
        
        // pick the scheme
        $scheme = $this->secure
                ? 'https://'
                : 'http://';
        
        // pick the host
        $host   = isset($server['HTTP_HOST'])
                ? $server['HTTP_HOST']
                : null;
        
        // pick the port
        $port   = isset($server['SERVER_PORT'])
                ? ':' . $server['SERVER_PORT']
                : null;
        
        // pick the uri
        $uri    = isset($server['REQUEST_URI'])
                ? $server['REQUEST_URI']
                : null;
        
        // construct the URL string
        $this->string = $scheme . $host . $port . $uri;
        
        // retain the URL parts
        $this->parts = parse_url($this->string);
    }
    
    /**
     * 
     * Returns the full URL string; 
     * or, if a component constant is passed, returns only that part of the URL
     * 
     * @param string $component
     * 
     * @return string
     * 
     */
    public function get($component = null)
    {
        if ($component === null) {
            return $this->string;
        }
        
        if (! isset($this->keys[$component])) {
            throw new Exception\InvalidComponent($component);
        }
        
        $key = $this->keys[$component];
        return isset($this->parts[$key])
             ? $this->parts[$key]
             : null;
    }
    
    /**
     * 
     * Indicates if the request is secure, whether via SSL, TLS, or
     * forwarded from a secure protocol
     * 
     * @return bool
     * 
     */
    public function isSecure()
    {
        return $this->secure;
    }
}
