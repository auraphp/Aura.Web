<?php
namespace Aura\Web\Request\Accept\Value;

class Media extends AbstractValue
{
    protected $type = '*';
    protected $subtype = '*';

    /**
     * @return string
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    public function isWildcard()
    {
        return $this->value == '*/*';
    }
    
    public function match(Media $avail)
    {
        // is it a full match?
        if (strtolower($this->value) == strtolower($avail->getValue())) {
            return $this->matchParameters($avail);
        }
        
        // is it a type match?
        return $this->subtype == '*'
            && strtolower($this->type) == strtolower($avail->getType())
            && $this->matchParameters($avail);
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    protected function prep()
    {
        list($this->type, $this->subtype) = explode('/', $this->value);
    }
}
