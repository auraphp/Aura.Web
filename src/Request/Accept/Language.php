<?php
namespace Aura\Web\Request\Accept;

class Language extends AbstractValues
{
    protected $server_key = 'HTTP_ACCEPT_LANGUAGE';

    protected $value_type = 'language';

    /**
     * 
     * Returns a language negotiated between acceptable and available values.
     * 
     * @param array $available Available values in preference order, if any.
     * 
     * @return mixed The header values as an array, or the negotiated value
     * (false indicates negotiation failed).
     * 
     */
    public function negotiate(array $available)
    {
        // if none available, no possible match
        if (! $available) {
            return false;
        }

        // convert to object
        $available = $this->convertAvailable($available);
        
        // if nothing acceptable specified, use first available
        if (! $this->acceptable) {
            return $available->get(0);
        }
        
        // loop through acceptable languages
        foreach ($this->acceptable as $accept) {
            
            // if the acceptable quality is zero, skip it
            if ($accept->getQuality() == 0) {
                continue;
            }
            
            // normalize values
            $value = strtolower($accept->getValue());
            $type = strtolower($accept->getType());
            
            // if acceptable language is *, return the first available
            if ($value == '*') {
                return $available->get(0);
            }
            
            // if acceptable language is available, use it
            foreach ($available as $avail) {
                
                // is it a full match?
                if ($value == strtolower($avail->getValue())) {
                    return $avail;
                }
                
                // not a full match, and subtype is specified, cannot match
                if ($accept->getSubtype()) {
                    continue;
                }
                
                // is it a type-without-subtype match?
                if ($type == strtolower($avail->getType())) {
                    return $avail;
                }
            }
        }
        
        // no match
        return false;
    }
}
