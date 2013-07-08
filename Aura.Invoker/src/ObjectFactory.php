<?php
namespace Aura\Invoker;

class ObjectFactory
{
    protected $map;
    
    public function __construct(array $map = [])
    {
        $this->map = $map;
    }
    
    public function set($name, $callable)
    {
        $this->map[$name] = $callable;
    }
    
    public function newInstance($spec)
    {
        $factory = $this->map[$spec];
        return $factory();
    }
}
