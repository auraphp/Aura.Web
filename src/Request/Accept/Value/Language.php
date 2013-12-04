<?php
namespace Aura\Web\Request\Accept\Value;

class Language extends \Aura\Web\Request\Accept\Value  {
    protected $type = '*';
    protected $subtype = false;

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
        $subtype = ($this->subtype) ? '-' .$this->subtype : '';
        return $this->type . $subtype;
    }

    public function setValue($value)
    {
        list($this->type, $this->subtype) = array_pad(explode('-', $value), 2, false);
    }

    public function __toString()
    {
        $parameters = (sizeof($this->parameters) > 0) ? ';' . http_build_query($this->getParameters(), null, ';') : '';

        return $this->getValue() . ';q=' . $this->priority . $parameters;
    }
}