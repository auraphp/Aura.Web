<?php
namespace Aura\Web\Response;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    protected $cache;
    
    protected function setUp()
    {
        $this->headers = new Headers;
        $this->cache = new Cache($this->headers);
    }
}

