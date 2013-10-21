<?php
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
    
    protected $status_code;
    
    protected $status_phrase;
    
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

    public function getStatusCode()
    {
        return $this->status_code;
    }
    
    public function getStatusPhrase()
    {
        return $this->status_phrase;
    }
    
    public function isWithoutCache()
    {
        return $this->without_cache;
    }
}
