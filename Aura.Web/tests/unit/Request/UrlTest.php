<?php
namespace Aura\Web\Request;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUrl()
    {
        $this->reset();
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/foo?bar=baz';
        $context = $this->newContext();
        
        $expect = 'http://example.com/foo?bar=baz';
        $actual = $context->getUrl();
        $this->assertSame($expect, $actual);
    }
    
    public function testIsSsl()
    {
        $this->reset();
        $client = $this->newContext();
        
        // HTTPS & SERVER_PORT not set
        $this->assertFalse($client->isSsl());
        
        $this->reset();
        $_SERVER['HTTPS'] = 'on';
        $client = $this->newContext();
        $this->assertTrue($client->isSsl());
        
        $this->reset();
        $_SERVER['SERVER_PORT'] = '443';
        $client = $this->newContext();
        $this->assertTrue($client->isSsl());
    }    
}
