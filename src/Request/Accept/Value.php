<?php
namespace Aura\Web\Request\Accept;

class Value {
    protected $value = "";
    protected $priority = 1.0;
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
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return float
     */
    public function getPriority()
    {
        return (float) $this->priority;
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