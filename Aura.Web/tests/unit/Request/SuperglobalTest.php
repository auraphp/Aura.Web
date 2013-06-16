<?php
namespace Aura\Web\Request;

class SuperglobalTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $super = new Superglobal(['foo' => 'bar']);
        
        $actual = $super->get('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $super->get('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $super->get('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $super->get();
        $this->assertSame(['foo' => 'bar'], $actual);
    }
}
