<?php

namespace aura\web;

class Exception_InvalidTokenFormat extends \Exception
{
    public function __construct()
    {
        parent::__construct('Malformed CSRF token.');
    }
}