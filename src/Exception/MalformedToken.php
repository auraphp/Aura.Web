<?php
namespace Aura\Web\Exception;
class MalformedToken extends \Aura\Web\Exception
{
    public function __construct()
    {
        parent::__construct('Malformed CSRF token.');
    }
}
