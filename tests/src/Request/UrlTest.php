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
        
        $expect = '/foo';
        $actual = $url->get(PHP_URL_PATH);
        $this->assertSame($expect, $actual);
        
        $this->setExpectedException('Aura\Web\Exception');
        $url->get('no such component');
    }
    
    public function testisSecure()
    {
        $url = $this->newUrl();
        $this->assertFalse($url->isSecure());
        
        $server = array('HTTPS' => 'on');
        $url = $this->newUrl($server);
        $this->assertTrue($url->isSecure());
        
        $server = array('SERVER_PORT' => '443');
        $url = $this->newUrl($server);
        $this->assertTrue($url->isSecure());
    }    
}
