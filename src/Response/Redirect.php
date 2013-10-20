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
namespace Aura\Web\Response;

use Aura\Web\Response;

class Redirect
{
    /**
     * 
     * Redirect to this location.
     * 
     * @var string
     * 
     */
    protected $location;
    
    /**
     * 
     * Status code
     * 
     * @var int
     * 
     */
    protected $status_code;
    
    /**
     * 
     * Status text
     * 
     * @var string
     * 
     */
    protected $status_phrase;
    
    /**
     * 
     * @var bool
     * 
     */
    protected $without_cache;
    
    
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
     * @return void
     * 
     */
    public function to($location, $code = 302, $phrase = 'Found')
    {
        $this->location      = $location;
        $this->status_code   = (int) $code;
        $this->status_phrase = $phrase;
        $this->without_cache = false;
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
     * @return void
     * 
     */
    public function withoutCache($location, $code = 303, $phrase = 'See Other')
    {
        $this->to($location, $code, $phrase);
        $this->without_cache = true;
    }

    /**
     * 
     * Returns the redirect location, if any.
     * 
     * @return string
     * 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * 
     * Status code
     * 
     * @return int
     * 
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }
    
    /**
     * 
     * Status text
     * 
     * @return string
     * 
     */
    public function getStatusPhrase()
    {
        return $this->status_phrase;
    }
    
    /**
     * 
     * @return bool True / False
     * 
     */
    public function isWithoutCache()
    {
        return $this->without_cache;
    }
    
    /**
     * 
     * Modify the Response object
     * 
     * @return void
     * 
     */
    public function modifyTransfer(Response $transfer)
    {
        if ($this->location) {
            $transfer->status->set($this->status_code, $this->status_phrase);
            $transfer->headers->set('Location', $this->location);
            if ($this->without_cache) {
                $transfer->cache->disable();
            }
        }
    }
}
