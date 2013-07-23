<?php
namespace Aura\Web\Request;

/**
 * 
 * Represents path-info paramters, typically via a router map.
 * 
 */
class Params extends Values
{
    public function set(array $data)
    {
        $this->data = $data;
    }
}
