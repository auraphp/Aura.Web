<?php
namespace Aura\Web\Request;

class HeadersTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHeader()
    {
        $this->reset();
        $_SERVER['HTTP_FOO'] = 'bar';
        $context = $this->newContext();
        
        $actual = $context->getHeader('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $context->getHeader('baz');
        $this->assertNull($actual);
        
        $actual = $context->getHeader('baz', 'dib');
        $this->assertSame('dib', $actual);
    }
    
    public function testXJsonIsRemoved()
    {
        $this->reset();
        $_SERVER['HTTP_X_JSON'] = 'remove-me';
        $context = $this->newContext();
        
        $actual = $context->getHeader('x-json');
        $this->assertNull($actual);
        
        $actual = $context->getServer('HTTP_X_JSON');
        $this->assertNull($actual);
    }
    
}
