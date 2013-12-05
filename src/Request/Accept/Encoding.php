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
        if (! $available) {
            return false;
        }

        $set = clone $this;
        $set->set(array());
        foreach ($available as $encoding) {
            $set->add($encoding);
        }
        $available = $set;

        // if no acceptable encoding specified, use first available
        if ($this->isEmpty()) {
            return $available->get(0);
        }
        
        // loop through acceptable encodings
        foreach ($this->acceptable as $encoding) {
            $value = strtolower($encoding->getValue());
            
            // if the acceptable quality is zero, skip it
            if ($encoding->getQuality() == 0) {
                continue;
            }
            
            // if acceptable encoding is *, return the first available
            if ($value == '*') {
                return $available->get(0);
            }
            
            // if acceptable encoding is available, use it
            foreach ($available as $avail) {
                if ($value == strtolower($avail->getValue())) {
                    return $avail;
                }
            }
        }
        
        return false;
    }
}
