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
 * Data transfer object for an HTTP response.
 * 
 * @todo Add a "finished" method to indicate that controllers should return
 * the response without further processing?
 * 
 * https://en.wikipedia.org/wiki/List_of_HTTP_headers
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
     * @var Response\Redirect
     * 
     */
    protected $redirect;
    
    /**
     * 
     * @var Response\Render
     * 
     */
    protected $render;
    
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
     * @param Response\Render $render
     * 
     * @param Response\Status $status
     * 
     */
    public function __construct(
        Response\Cache    $cache,
        Response\Content  $content,
        Response\Cookies  $cookies,
        Response\Headers  $headers,
        Response\Redirect $redirect,
        Response\Render   $render,
        Response\Status   $status
    ) {
        $this->cache    = $cache;
        $this->content  = $content;
        $this->cookies  = $cookies;
        $this->headers  = $headers;
        $this->redirect = $redirect;
        $this->render   = $render;
        $this->status   = $status;
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
     * Creates and returns a data transfer object assembled from the response
     * properties.
     * 
     * @return StdClass A StdClass object with properties $status, $headers,
     * $cookies, and $content.
     * 
     */
    public function getTransfer()
    {
        $status   = clone $this->status;
        $headers  = clone $this->headers;
        $cookies  = clone $this->cookies;
        $content  = clone $this->content;
        $cache    = clone $this->cache;
        $redirect = clone $this->redirect;
        
        // set the content type
        $type = $content->getType();
        if ($type) {
            $charset = $content->getCharset();
            if ($charset) {
                $type .= '; charset=' . $charset;
            }
            $headers->set('Content-Type', $type);
        }

        // set the content disposition
        $disposition = $content->getDisposition();
        if ($disposition) {
            $filename = $content->getFilename();
            if ($filename) {
                $disposition .='; filename='. $filename;
            }
            $headers->set('Content-Disposition', $disposition);
        }
        
        // set a redirect location
        $location = $redirect->getLocation();
        if ($location) {
            $status->set(
                $redirect->getStatusCode(),
                $redirect->getStatusPhrase()
            );
            $headers->set('Location', $location);
            if ($redirect->isWithoutCache()) {
                $cache->disable();
            }
        }
        
        // disable the cache
        if ($cache->isDisabled()) {
            $headers->set('Pragma', 'no-cache');
            $headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            $headers->set('Expires', '1');
        }
        
        // return a transfer object
        return (object) array(
            'status'  => $status->get(),
            'headers' => $headers->get(),
            'cookies' => $cookies->get(),
            'content' => $content->get(),
        );
    }
}
