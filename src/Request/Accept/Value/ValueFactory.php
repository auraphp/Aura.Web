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
 * A factory to create value objects.
 * 
 * @package Aura.Web
 * 
 */
class ValueFactory
{
    /**
     * 
     * A map of value types to value classes.
     * 
     * @var array
     * 
     */
    protected $map = array(
        'charset' => 'Aura\Web\Request\Accept\Value\Charset',
        'encoding' => 'Aura\Web\Request\Accept\Value\Encoding',
        'language' => 'Aura\Web\Request\Accept\Value\Language',
        'media' => 'Aura\Web\Request\Accept\Value\Media',
    );
    
    /**
     * 
     * Returns a new value object instance.
     * 
     * @param string $type The value type.
     * 
     * @param string $value The acceptable value.
     * 
     * @param float $quality The quality parameter.
     * 
     * @param array $parameters Additional parameters.
     * 
     * @return AbstractValue
     * 
     */
    public function newInstance($type, $value, $quality, array $parameters)
    {
        $class = $this->map[$type];
        return new $class($value, $quality, $parameters);
    }
}
