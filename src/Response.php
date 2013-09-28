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

use Aura\Web\Response\PropertyFactory;

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
    protected $cache;
    protected $content;
    protected $cookies;
    protected $headers;
    protected $redirect;
    protected $render;
    protected $status;
    
    public function __construct(PropertyFactory $property_factory)
    {
        $this->cache    = $property_factory->newCache();
        $this->content  = $property_factory->newContent();
        $this->cookies  = $property_factory->newCookies();
        $this->headers  = $property_factory->newHeaders();
        $this->redirect = $property_factory->newRedirect();
        $this->render   = $property_factory->newRender();
        $this->status   = $property_factory->newStatus();
    }
    
    public function __clone()
    {
        $this->cache    = clone $this->cache;
        $this->content  = clone $this->content;
        $this->cookies  = clone $this->cookies;
        $this->headers  = clone $this->headers;
        $this->redirect = clone $this->redirect;
        $this->render   = clone $this->render;
        $this->status   = clone $this->status;
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
     * @return StdClass
     * 
     */
    public function getTransfer()
    {
        $transfer = clone $this;
        $transfer->content->modifyTransfer($transfer);
        $transfer->redirect->modifyTransfer($transfer);
        $transfer->cache->modifyTransfer($transfer);
        return (object) array(
            'status'  => $transfer->status->get(),
            'headers' => $transfer->headers->get(),
            'cookies' => $transfer->cookies->get(),
            'content' => $transfer->content->get(),
        );
    }
}
