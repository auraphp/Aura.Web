<?php
namespace Aura\Web\Request\Accept\Value;

abstract class AbstractValue
{
    protected $value = '';
    protected $quality = 1.0;
    protected $parameters = array();

    public function __construct(
        $value,
        $quality,
        array $parameters
    ) {
        $this->value = $value;
        $this->quality = $quality;
        $this->parameters = $parameters;
        $this->prep();
    }
    
    protected function prep()
    {
        // do nothing
    }
    
    public function isWildcard()
    {
        return $this->value == '*';
    }
    
    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return float
     */
    public function getQuality()
    {
        return (float) $this->quality;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
