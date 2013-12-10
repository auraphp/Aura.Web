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
 * Represents an encoding value.
 * 
 * @package Aura.Web
 * 
 */
class Encoding extends AbstractValue
{
    /**
     * 
     * Checks if an available encoding value matches this acceptable value.
     * 
     * @param Encoding $avail An available encoding value.
     * 
     * @return True on a match, false if not.
     * 
     */
    public function match(Encoding $avail)
    {
        return strtolower($this->value) == strtolower($avail->getValue())
            && $this->matchParameters($avail);
    }
}
