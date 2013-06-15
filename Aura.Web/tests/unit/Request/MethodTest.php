<?php
namespace Aura\Web\Request;

class MethodTest extends \PHPUnit_Framework_TestCase
{
    public function testIsGet()
    {
        $this->reset();
        $context = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($context->isGet());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $context = $this->newContext();
        $this->assertTrue($context->isGet());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'NOT-GET';
        $context = $this->newContext();
        $this->assertFalse($context->isGet());
    }

    public function testIsPost()
    {
        $this->reset();
        $context = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($context->isPost());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $context = $this->newContext();
        $this->assertTrue($context->isPost());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'NOT-POST';
        $context = $this->newContext();
        $this->assertFalse($context->isPost());
    }

    public function testIsPut()
    {
        $this->reset();
        $context = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($context->isPut());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $context = $this->newContext();
        $this->assertTrue($context->isPut());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'NOT-PUT';
        $context = $this->newContext();
        $this->assertFalse($context->isPut());
    }

    public function testIsDelete()
    {
        $this->reset();
        $context = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($context->isDelete());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $context = $this->newContext();
        $this->assertTrue($context->isDelete());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'NOT-DELETE';
        $context = $this->newContext();
        $this->assertFalse($context->isDelete());
    }

    public function testIsHead()
    {
        $this->reset();
        $context = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($context->isHead());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $context = $this->newContext();
        $this->assertTrue($context->isHead());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'NOT-HEAD';
        $context = $this->newContext();
        $this->assertFalse($context->isHead());
    }

    public function testIsOptions()
    {
        $this->reset();
        $context = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($context->isOptions());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        $context = $this->newContext();
        $this->assertTrue($context->isOptions());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'NOT-OPTIONS';
        $context = $this->newContext();
        $this->assertFalse($context->isOptions());
    }

    public function testHttpMethodOverload()
    {
        $this->reset();
        $_POST['X-HTTP-Method-Override']        = 'header-takes-precedence';
        $_SERVER['REQUEST_METHOD']              = 'POST';
        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'PUT';
        $context    = $this->newContext();
        $actual = $context->getServer('REQUEST_METHOD');
        
        $this->assertSame('PUT', $actual);
        
        $this->reset();
        $_POST['X-HTTP-Method-Override']        = 'DELETE';
        $_SERVER['REQUEST_METHOD']              = 'POST';
        $context    = $this->newContext();
        $actual = $context->getServer('REQUEST_METHOD');
        
        $this->assertSame('DELETE', $actual);
    }    
}
