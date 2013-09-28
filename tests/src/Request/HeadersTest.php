<?php
namespace Aura\Web\Request;

class HeadersTest extends \PHPUnit_Framework_TestCase
{
    protected function newHeaders($server = array())
    {
        return new Headers($server);
    }
    
    public function testGet()
    {
        $server['HTTP_FOO'] = 'bar';
        $server['HTTP_QUX'] = 'quux';
        $headers = $this->newHeaders($server);
        
        $actual = $headers->get('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $headers->get('baz');
        $this->assertNull($actual);
        
        $actual = $headers->get('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        $expect = array('foo' => 'bar', 'qux' => 'quux');
        $actual = $headers->get();
        $this->assertSame($expect, $actual);
    }
}
