<?php
/**
 * 
 * This file is part of Aura for PHP.
 * 
 * @package Aura.Web
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web\Request\Accept\Value;

/**
 * 
 * Represents an acceptable value.
 * 
 * @package Aura.Web
 * 
 */
abstract class AbstractValue
{
    /**
     * 
     * The acceptable value, not including any parameters.
     * 
     * @var string
     * 
     */
    protected $value;
    
    /**
     * 
     * The quality parameter.
     * 
     * @var float
     * 
     */
    protected $quality = 1.0;
    
    /**
     * 
     * Parameters attached to the acceptable value.
     * 
     * @var array
     * 
     */
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
    
    protected function matchParameters(AbstractValue $avail)
    {
        foreach ($avail->getParameters() as $label => $value) {
            if ($this->parameters[$label] != $value) {
                return false;
            }
        }
        return true;
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
