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
namespace Aura\Web\Request;

use ArrayObject;

/**
 * 
 * A representation of read-only values, generally superglobal values.
 * 
 * @package Aura.Web
 * 
 */
class Values extends ArrayObject
{
    /**
     * 
     * Returns the value of an array key, or an alternative value if not set.
     * 
     * @param string $key The array key to return.
     * 
     * @param string $alt The alternative value if the key is not set.
     * 
     * @return mixed The value of the array key, or the alternative value if
     * not set.
     * 
     */
    public function get($key = null, $alt = null)
    {
        if (! $key) {
            return $this->getArrayCopy();
        }
        
        if (isset($this[$key])) {
            return $this[$key];
        }
        
        return $alt;
    }
}
