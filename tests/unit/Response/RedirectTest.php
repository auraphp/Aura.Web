<?php
namespace Aura\Web\Response;

class RedirectTest extends \PHPUnit_Framework_TestCase
{
    protected $redirect;
    
    protected function setUp()
    {
        $this->redirect = new Redirect;
    }

    public function testTo()
    {
        $this->redirect->to('http://example.com');
        $this->assertSame('http://example.com', $this->redirect->getLocation());
        $this->assertSame(302, $this->redirect->getStatusCode());
        $this->assertSame('Found', $this->redirect->getStatusPhrase());
        $this->assertFalse($this->redirect->isWithoutCache());
    }
    
    public function testWithoutCache()
    {
        $this->redirect->withoutCache('http://example.com');
        $this->assertSame('http://example.com', $this->redirect->getLocation());
        $this->assertSame(303, $this->redirect->getStatusCode());
        $this->assertSame('See Other', $this->redirect->getStatusPhrase());
        $this->assertTrue($this->redirect->isWithoutCache());
    }
}
