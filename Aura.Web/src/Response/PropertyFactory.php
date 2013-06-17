<?php
namespace Aura\Web\Response;

use ArrayObject;

class PropertyFactory
{
    public function newCache()
    {
        return new Cache;
    }
    
    public function newContent()
    {
        return new Content;
    }
    
    public function newCookies()
    {
        return new Cookies;
    }
    
    public function newData()
    {
        return new ArrayObject([]);
    }
    
    public function newHeaders()
    {
        return new Headers;
    }
    
    public function newRedirect()
    {
        return new Redirect;
    }

    public function newStatus()
    {
        return new Status;
    }
}
