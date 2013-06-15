<?php
namespace Aura\Web\Request;

class SuperglobalTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $this->reset();
        $_GET['foo'] = 'bar';
        $context = $this->newContext();
        
        $actual = $context->getQuery('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $context->getQuery('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $context->getQuery('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $context->getQuery();
        $this->assertSame(['foo' => 'bar'], $actual);
    }
    
    public function testGetPost()
    {
        $this->reset();
        $_POST['foo'] = 'bar';
        $context = $this->newContext();
        
        $actual = $context->getPost('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $context->getPost('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $context->getPost('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $context->getPost();
        $this->assertSame(['foo' => 'bar'], $actual);
    }

    public function testGetCookie()
    {
        $this->reset();
        $_COOKIE['foo'] = 'bar';
        $context = $this->newContext();
        
        $actual = $context->getCookie('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $context->getCookie('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $context->getCookie('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $context->getCookie();
        $this->assertSame(['foo' => 'bar'], $actual);
    }

    public function testGetEnv()
    {
        $this->reset();
        $_ENV['foo'] = 'bar';
        $context = $this->newContext();
        
        $actual = $context->getEnv('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $context->getEnv('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $context->getEnv('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $context->getEnv();
        $this->assertSame(['foo' => 'bar'], $actual);
    }

    public function testGetServer()
    {
        $this->reset();
        $_SERVER['foo'] = 'bar';
        $context = $this->newContext();
        
        $actual = $context->getServer('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $context->getServer('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $context->getServer('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $context->getServer();
        $this->assertSame(['foo' => 'bar'], $actual);
    }
}
