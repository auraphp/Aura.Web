<?php
namespace Aura\Web\Request;

class Url
{
    protected $value;
    
    protected $secure;
    
    public function __construct(array $server)
    {
        $https = isset($server['HTTPS'])
               ? (strtolower($server['HTTPS']) == 'on')
               : false;
        
        $port  = isset($server['SERVER_PORT'])
               ? ($server['SERVER_PORT'] == 443)
               : false;
        
        $fwd   = isset($server['HTTP_X_FORWARDED_PROTO'])
               ? (strtolower($server['HTTP_X_FORWARDED_PROTO']) == 'https')
               : false;
               
        $this->secure = ($https || $port || $fwd);
        
        $scheme = $this->secure
                ? 'https://'
                : 'http://';
        
        $host   = isset($server['HTTP_HOST'])
                ? $server['HTTP_HOST']
                : null;
        
        $port   = isset($server['SERVER_PORT'])
                ? ':' . $server['SERVER_PORT']
                : null;
        
        $uri    = isset($server['REQUEST_URI'])
                ? $server['REQUEST_URI']
                : null;
        
        $this->value = $scheme . $host . $port . $uri;
    }
    
    public function get()
    {
        return $this->value;
    }
    
    public function isSecure()
    {
        return $this->secure;
    }
}
