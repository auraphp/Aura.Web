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

use DateTime;
use DateTimeZone;
use RuntimeException;

/**
 * 
 * @todo Add the following:
 * 
 * Cache-Control -- tells all caching mechanisms from server to client whether
 * they may cache this object. It is measured in seconds.
 * Cache-Control: max-age=3600 Permanent
 * 
 * Expires -- gives the date/time after which the response is considered
 * stale.
 * Expires: Thu, 01 Dec 1994 16:00:00 GMT 
 * 
 * Last-Modified -- The last modified date for the requested object, in RFC
 * 2822 format.
 * Last-Modified: Tue, 15 Nov 1994 12:45:26 GMT
 * 
 * Vary -- tells downstream proxies how to match future request headers to
 * decide whether the cached response can be used rather than requesting a
 * fresh one from the origin server.
 * 
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

    protected $control = array();
    
    protected $headers;
    
    public function __construct($headers)
    {
        $this->headers = $headers;
        $this->reset();
    }
    
    /**
     * 
     * Disable HTTP caching.
     * 
     * @return null
     * 
     */
    public function disable()
    {
        $this->setAge(null);
        $this->setControl(array(
            'public' => false,
            'private' => true,
            'max-age' => 0,
            's-maxage' => 0,
            'no-cache' => true,
            'no-store' => true,
            'must-revalidate' => true,
            'proxy-revalidate' => true,
        ));
        $this->setEtag(null);
        $this->setExpires(1);
        $this->setLastModified(null);
        $this->setVary(null);
    }
    
    /**
     * 
     * Reset caching headers to their original state (i.e., no caching
     * headers).
     * 
     * @return null
     * 
     */
    public function reset()
    {
        $this->setAge(null);
        $this->setControl(array(
            'public' => false,
            'private' => false,
            'max-age' => 0,
            's-maxage' => 0,
            'no-cache' => false,
            'no-store' => false,
            'must-revalidate' => false,
            'proxy-revalidate' => false,
        ));
        $this->setEtag(null);
        $this->setExpires(null);
        $this->setLastModified(null);
        $this->setVary(null);
    }
    
    public function setAge($age)
    {
        $this->headers->set('Age', (int) $age);
    }
    
    public function setControl(array $control)
    {
        // prepare the cache-control directives
        $this->control = array_merge($this->control, $control);
        
        // turn off public/private if no caching
        if ($this->control['no-cache']) {
            $this->control['public'] = false;
            $this->control['private'] = false;
        }
        
        // shared max-age indicates public
        if ($this->control['s_maxage']) {
            $this->control['public'] = true;
            $this->control['private'] = false;
        }
        
        // collect the control directives
        $control = array();
        foreach ($this->control as $key => $val) {
            if ($val === true) {
                // flag
                $control[] = $key;
            } elseif ($val) {
                // value
                $control[] = "$key=$val";
            }
        }
        
        // set the header; clears cache-control if empty
        $this->headers->set('Cache-Control', implode(', ', $control));
        
        // if we have no-cache, also send pragma
        if ($this->control['no-cache']) {
            $this->headers->set('Pragma', 'no-cache');
        } else {
            $this->headers->set('Pragma', null);
        }
    }
    
    public function setEtag($etag)
    {
        $this->headers->set('Etag', '"' . $etag . '"');
    }
    
    public function setExpires($expires)
    {
        $this->headers->set('Expires', $this->fixDate($expires));
    }
    
    public function setLastModified($last_modified)
    {
        $this->headers->set('Last-Modified', $this->fixDate($last_modified));
    }
    
    public function setMaxAge($max_age)
    {
        $this->setControl(array(
            'max-age' => (int) $max_age,
        ));
    }
    
    public function setNoCache()
    {
        $this->setControl(array(
            'no-cache' => (bool) $flag
        ));
    }
    
    public function setNoStore($flag = true)
    {
        $this->setControl(array(
            'no-store' => (bool) $flag
        ));
    }
    
    public function setPrivate()
    {
        $this->setControl(array(
            'public' => false,
            'private' => true,
        ));
    }
    
    public function setPublic()
    {
        $this->setControl(array(
            'public' => true,
            'private' => false,
        ));
    }
    
    public function setSharedMaxAge($s_maxage)
    {
        $this->setControl(array(
            's-maxage' => (int) $s_maxage
        ));
    }
    
    public function setVary($vary)
    {
        $this->headers->set('Vary', implode(', ', (array) $vary));
    }
    
    public function setWeakEtag($etag)
    {
        $this->headers->set('Etag', 'W/"' . $etag . '"');
    }
    
    protected function fixDate($date)
    {
        if ($date instanceof DateTime) {
            $date = clone $date;
        } else {
            try {
                $date = new DateTime($date);
            } catch (RuntimeException $e) {
                // treat bad dates as being in the past
                $date = new DateTime(1);
            }
        }
        
        $date->setTimeZone(new DateTimeZone('UTC'));
        return $date->format('D, d M Y H:i:s') . ' GMT';
    }
}
