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
 * Represents an acceptable charset value.
 * 
 * @package Aura.Web
 * 
 */
class Charset extends AbstractValue
{
    /**
     * 
     * Checks if an available charset value matches this acceptable value.
     * 
     * @param Charset $avail An available charset value.
     * 
     * @return True on a match, false if not.
     * 
     */
    public function match(Charset $avail)
    {
        return strtolower($this->value) == strtolower($avail->getValue())
            && $this->matchParameters($avail);
    }
}
