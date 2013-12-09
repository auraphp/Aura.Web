<?php
namespace Aura\Web\Request\Accept;

class Encoding extends AbstractValues
{
    protected $server_key = 'HTTP_ACCEPT_ENCODING';

    protected $value_type = 'encoding';
    
    /**
     * 
     * Returns an encoding negotiated between acceptable and available values.
     * 
     * @param array $available Available values in preference order, if any.
     * 
     * @return mixed The header values as an array, or the negotiated value
     * (false indicates negotiation failed).
     * 
     * @todo identity encoding is always acceptable unless set explictly to q=0
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
        
        // loop through acceptable encodings
        foreach ($this->acceptable as $accept) {
            
            // if the acceptable quality is zero, skip it
            if ($accept->getQuality() == 0) {
                continue;
            }
            
            // normalize the value
            $value = strtolower($accept->getValue());
            
            // if acceptable encoding is *, return the first available
            if ($accept->isWildcard()) {
                return $available->get(0);
            }
            
            // if acceptable encoding is available, use it
            foreach ($available as $avail) {
                if ($value == strtolower($avail->getValue())) {
                    return $avail;
                }
            }
        }
        
        // no match
        return false;
    }
}
