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
        if (! $this->acceptable) {
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
}
