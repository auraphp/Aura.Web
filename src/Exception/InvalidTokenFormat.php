<?php

namespace aura\web;

class Exception_InvalidTokenFormat extends \Exception
{
    public function __construct()
    {
        parent::__construct('Invalid format for a CSRF token.');
    }
}