<?php
namespace Aura\Web\Response;

class Redirect
{
    public function __construct(
        Status $status,
        Headers $headers,
        Cache $cache
    ) {
        $this->status = $status;
        $this->headers = $headers;
        $this->cache = $cache;
    }
    
    public function to($location, $code = 302, $phrase = null)
    {
        $this->headers->set('Location', $location);
        $this->status->setCode($code);
        if ($phrase) {
            $this->status->setPhrase($phrase);
        }
    }

    public function afterPost($location)
    {
        $this->seeOther($location);
    }
    
    public function created($location)
    {
        $this->to($location, 201);
    }
    
    public function movedPermanently($location)
    {
        $this->to($location, 301);
    }
    
    public function found($location)
    {
        $this->to($location, 302);
    }
    
    public function seeOther($location)
    {
        $this->to($location, 303);
        $this->cache->disable();
    }
    
    public function temporaryRedirect($location)
    {
        $this->to($location, 307);
    }
    
    public function permanentRedirect($location)
    {
        $this->to($location, 308);
    }
}
