<?php
namespace Aura\Web\Request\Accept;

class Charset extends AbstractValues
{
    protected $server_key = 'HTTP_ACCEPT_CHARSET';
    
    protected $value_class = 'Aura\Web\Request\Accept\Value\Charset';
    
    /**
     * @param array $server A copy of $_SERVER.
     */
    public function __construct(array $server = array())
    {
        parent::__construct($server);
        
        // are charset values specified?
        if (count($this->values) == 0) {
            // no, so don't modify anything
            return;
        }
        
        // look for ISO-8859-1, case insensitive
        foreach ($this->values as $charset) {
            if (strtolower($charset->getValue()) == 'iso-8859-1') {
                // found it, no no need to modify
                return;
            }
        }
        
        // charset ISO-8859-1 is acceptable if not explictly mentioned
        $this->addValues('ISO-8859-1');
    }
    /**
     * 
     * Returns a negotiated value between acceptable and available values.
     * 
     * @param array $available Available values in preference order, if any.
     * 
     * @return mixed The negotiated value, or false if negotiation failed.
     * 
     */
    public function negotiate(array $available)
    {
        if (! $available) {
            return false;
        }

        $set = clone $this;
        $set->setValues(array());
        foreach ($available as $charset) {
            $set->addValues($charset);
        }
        $available = $set;
        
        // get acceptable charsets
        $acceptable = $this->values;
        
        // if no acceptable charset specified, use first available
        if (count($acceptable) == 0) {
            return $available[0];
        }
        
        // loop through acceptable charsets
        foreach ($acceptable as $charset) {
            $value = strtolower($charset->getValue());
            
            // if the acceptable quality is zero, skip it
            if ($charset->getPriority() == 0) {
                continue;
            }
            
            // if acceptable charset is *, return the first available
            if ($value == '*') {
                return $available[0];
            }
            
            // if acceptable charset is available, use it
            foreach ($available as $avail) {
                if ($value == strtolower($avail->getValue())) {
                    return $avail;
                }
            }
        }
        
        return false;
    }
}
