<?php
namespace Aura\Web\Request;

class ValuesTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $values = new Values(array('foo' => 'bar'));
        
        $actual = $values->get('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $values->get('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $values->get('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $values->get();
        $this->assertSame(array('foo' => 'bar'), $actual);
    }
}
