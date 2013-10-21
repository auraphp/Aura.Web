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

/**
 * 
 * A factory for creating Context and Stdio objects.
 * 
 * @package Aura.Web
 * 
 */
class Values
{
    protected $data;
    
    /**
     * 
     * @param array $data
     * 
     */
    public function __construct(array $data = array())
    {
        $this->data = $data;
    }
    
    /**
     * 
     * @param string $key
     * 
     * @param string $alt
     * 
     * @return string $alt
     * 
     */
    public function get($key = null, $alt = null)
    {
        if (! $key) {
            return $this->data;
        }
        
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        
        return $alt;
    }
}
