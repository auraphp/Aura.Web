<?php
namespace Aura\Web\Request\Accept;

class Value {
    protected $value = "";
    protected $quality = 1.0;
    protected $parameters = array();

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param int $quality
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;
    }

    /**
     * @return float
     */
    public function getQuality()
    {
        return (float) $this->quality;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    public function __toString()
    {
        return $this->getValue();
    }
}