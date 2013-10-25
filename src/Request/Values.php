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
 * A factory for creating Context and Stdio objects.
 * 
 * @package Aura.Web
 * 
 */
class Values extends ArrayObject
{
    /**
     * 
     * @param string $key
     * 
     * @param string $alt
     * 
     * @return string
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
