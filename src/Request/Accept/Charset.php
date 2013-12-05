<?php
namespace Aura\Web\Request\Accept;

class Charset extends AbstractValues
{
    protected $server_key = 'HTTP_ACCEPT_CHARSET';
    
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
}
