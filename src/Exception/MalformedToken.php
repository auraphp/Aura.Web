<?php
/**
 * 
 * This file is part of the Aura Project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web\Exception;
class MalformedToken extends \Aura\Web\Exception
{
    public function __construct()
    {
        parent::__construct('Malformed CSRF token.');
    }
}
