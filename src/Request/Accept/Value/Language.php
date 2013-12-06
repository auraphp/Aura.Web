<?php
namespace Aura\Web\Request\Accept\Value;

class Language extends AbstractValue
{
    protected $type = '*';
    protected $subtype = false;

    /**
     * @return string
     */
    public function getSubtype()
    {
        return $this->subtype;
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

    protected function prep()
    {
        list($this->type, $this->subtype) = array_pad(explode('-', $this->value), 2, false);
    }
}
