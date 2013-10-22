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
        Response\Cache    $cache
    ) {
        $this->status   = $status;
        $this->headers  = $headers;
        $this->cookies  = $cookies;
        $this->content  = $content;
        $this->cache    = $cache;
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
    
    /**
     * 
     * Set a location that the response should redirect to, along with a
     * a status code and status phrase.
     * 
     * @param string $location The URI to redirect to.
     * 
     * @param int $code The HTTP status code to redirect with; default
     * is `302`.
     * 
     * @param string $phrase The HTTP status phrase; default is `'Found'`.
     * 
     * @return null
     * 
     */
    public function redirect($location, $code = 302, $phrase = 'Found')
    {
        $this->headers->set('Location', $location);
        $this->status->setCode($code);
        $this->status->setPhrase($phrase);
    }

    /**
     * 
     * Set a location that the response should redirect to, along with a
     * a status code and status phrase, *and* disables cache.
     * 
     * @param string $location The URL to redirect to.
     * 
     * @param int|string $code The HTTP status code to redirect with; default
     * is `303`.
     * 
     * @param string $phrase The HTTP status phrase; default is `'See Other'`.
     * 
     * @return null
     * 
     */
    public function redirectNoCache($location, $code = 303, $phrase = 'See Other')
    {
        $this->redirect($location, $code, $phrase);
        $this->cache->disable();
    }
}
