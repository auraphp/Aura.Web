<?php
namespace Aura\Web\Request\Accept;

use Aura\Web\Request\Accept\Value\ValueFactory;

class Charset extends AbstractValues
{
    protected $server_key = 'HTTP_ACCEPT_CHARSET';
    
    protected $value_type = 'charset';
    
    /**
     * @param array $server A copy of $_SERVER.
     */
    public function __construct(
        ValueFactory $value_factory,
        array $server = array()
    ) {
        parent::__construct($value_factory, $server);
        
        // are charset values specified?
        if ($this->isEmpty()) {
            // no, so don't modify anything
            return;
        }
        
        // look for ISO-8859-1, case insensitive
        foreach ($this->acceptable as $charset) {
            if (strtolower($charset->getValue()) == 'iso-8859-1') {
                // found it, no no need to modify
                return;
            }
        }
        
        // charset ISO-8859-1 is acceptable if not explictly mentioned
        $this->add('ISO-8859-1');
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
        $set->set(array());
        foreach ($available as $charset) {
            $set->add($charset);
        }
        $available = $set;
        
        // if no acceptable charset specified, use first available
        if ($this->isEmpty()) {
            return $available->get(0);
        }
        
        // loop through acceptable charsets
        foreach ($this->acceptable as $charset) {
            $value = strtolower($charset->getValue());
            
            // if the acceptable quality is zero, skip it
            if ($charset->getQuality() == 0) {
                continue;
            }
            
            // if acceptable charset is *, return the first available
            if ($value == '*') {
                return $available->get(0);
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
