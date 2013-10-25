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
 * Represents path-info paramters, typically via a router map.
 * 
 * @package Aura.Web
 * 
 */
class Params extends Values
{
    public function set(array $params)
    {
        $this->exchangeArray($params);
    }
}
