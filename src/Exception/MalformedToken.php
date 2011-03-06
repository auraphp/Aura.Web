<?php

namespace aura\web;

class MalformedToken extends \Exception
{
    public function __construct()
    {
        parent::__construct('Malformed CSRF token.');
    }
}