<?php
namespace Aura\Web\Request;

class Values
{
    protected $data;
    
    public function __construct(array $data = array())
    {
        $this->data = $data;
    }
    
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
