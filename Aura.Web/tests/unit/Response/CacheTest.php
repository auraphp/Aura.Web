<?php
namespace Aura\Web\Response;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    protected $cache;
    
    protected function setUp()
    {
        $this->cache = new Cache;
    }
    
    public function test()
    {
        $this->assertFalse($this->cache->isDisabled());
        $this->cache->disable();
        $this->assertTrue($this->cache->isDisabled());
    }
}

