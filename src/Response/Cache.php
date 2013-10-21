<?php
namespace Aura\Web\Response;

use Aura\Web\Response;

/*
Cache-Control 	Tells all caching mechanisms from server to client whether they may cache this object. It is measured in seconds 	Cache-Control: max-age=3600 	Permanent
Expires 	Gives the date/time after which the response is considered stale 	Expires: Thu, 01 Dec 1994 16:00:00 GMT 	Permanent: standard
Last-Modified 	The last modified date for the requested object, in RFC 2822 format 	Last-Modified: Tue, 15 Nov 1994 12:45:26 GMT
Vary 	Tells downstream proxies how to match future request headers to decide whether the cached response can be used rather than requesting a fresh one from the origin server. 	Vary: * 	Permanent
*/
class Cache
{
    /**
     * 
     * Should the response disable HTTP caching?
     * 
     * @var bool
     * 
     */
    protected $disabled = false;

    /**
     * 
     * Should the response disable HTTP caching?
     * 
     * @param bool $disable When true, disable HTTP caching.
     * 
     * @return void
     * 
     */
    public function disable($disabled = true)
    {
        $this->disabled = (bool) $disabled;
    }

    /**
     * 
     * Is caching turned off?
     * 
     * @return bool
     * 
     */
    public function isDisabled()
    {
        return $this->disabled;
    }
}
