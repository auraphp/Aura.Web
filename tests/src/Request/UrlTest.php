<?php
namespace Aura\Web\Request;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    protected function newUrl($server = array())
    {
        return new Url($server);
    }
    
    public function testGet()
    {
        $server['HTTP_HOST'] = 'example.com';
        $server['REQUEST_URI'] = '/foo?bar=baz';
        $url = $this->newUrl($server);
        
        $expect = 'http://example.com/foo?bar=baz';
        $actual = $url->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testIsSsl()
    {
        $url = $this->newUrl();
        $this->assertFalse($url->isSsl());
        
        $server = array('HTTPS' => 'on');
        $url = $this->newUrl($server);
        $this->assertTrue($url->isSsl());
        
        $server = array('SERVER_PORT' => '443');
        $url = $this->newUrl($server);
        $this->assertTrue($url->isSsl());
    }    
}
