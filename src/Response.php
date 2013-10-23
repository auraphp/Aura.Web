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
 * Descriptors for building an HTTP response; note that this is not itself an
 * HTTP response.
 * 
 * @package Aura.Web
 * 
 */
class Response
{
    /**
     * 
     * @var Response\Cache
     * 
     */
    protected $cache;
    
    /**
     * 
     * @var Response\Content
     * 
     */
    protected $content;
    
    /**
     * 
     * @var Response\Cookies
     * 
     */
    protected $cookies;
    
    /**
     * 
     * @var Response\Headers
     * 
     */
    protected $headers;
    
    /**
     * 
     * @var Response\Status
     * 
     */
    protected $status;

    /**
     * 
     * Constructor
     * 
     * @param Response\Cache $cache
     * 
     * @param Response\Content $cache
     * 
     * @param Response\Cookies $cookies
     * 
     * @param Response\Headers $headers
     * 
     * @param Response\Redirect $redirect
     * 
     * @param Response\Status $status
     * 
     */
    public function __construct(
        Response\Status   $status,
        Response\Headers  $headers,
        Response\Cookies  $cookies,
        Response\Content  $content,
        Response\Cache    $cache,
        Response\Redirect $redirect
    ) {
        $this->status   = $status;
        $this->headers  = $headers;
        $this->cookies  = $cookies;
        $this->content  = $content;
        $this->cache    = $cache;
        $this->redirect = $redirect;
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
}
