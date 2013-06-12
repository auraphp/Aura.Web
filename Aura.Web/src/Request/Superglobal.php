<?php
namespace Aura\Web\Request;

class Superglobal
{
    protected $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function get($key, $alt = null)
    {
        if (array_key_exists($this->data[$key])) {
            return $this->data[$key];
        }
        return $alt;
    }
}
