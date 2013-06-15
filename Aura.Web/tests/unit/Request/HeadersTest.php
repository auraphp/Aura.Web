<?php
namespace Aura\Web\Request;

class HeadersTest extends \PHPUnit_Framework_TestCase
{
    protected function newHeaders($server = [])
    {
        return new Headers($server);
    }
    
    public function testGet()
    {
        $server['HTTP_FOO'] = 'bar';
        $headers = $this->newHeaders($server);
        
        $actual = $headers->get('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $headers->get('baz');
        $this->assertNull($actual);
        
        $actual = $headers->get('baz', 'dib');
        $this->assertSame('dib', $actual);
    }
}
