<?php
namespace Aura\Web\Request\Accept\Value;

class Media extends \Aura\Web\Request\Accept\Value
{
    protected $type = '*';
    protected $subtype = '*';

    /**
     * @param string $subtype
     */
    public function setSubtype($subtype)
    {
        $this->subtype = $subtype;
    }

    /**
     * @return string
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->type. '/' .$this->subtype;
    }

    public function setValue($value)
    {
        list($this->type, $this->subtype) = explode('/', $value);
    }

    public function __toString()
    {
        $parameters = (count($this->parameters) > 0) ? ';' . http_build_query($this->getParameters(), null, ';') : '';

        return $this->getValue() . ';q=' . $this->quality . $parameters;
    }
}