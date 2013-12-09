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
            return true;
        }
        
        // is it a type match?
        return $this->subtype == '*' &&
            && $this->type == $avail->getType();
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

    public function __toString()
    {
        $parameters = (count($this->parameters) > 0) ? ';' . http_build_query($this->getParameters(), null, ';') : '';

        return $this->getValue() . ';q=' . $this->quality . $parameters;
    }
}
