<?php
namespace Aura\Web;

use Aura\Web\Response\PropertyFactory;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    protected $response;

    protected $headers;
    
    protected function setUp()
    {
        parent::setUp();
        $globals = array();
        $factory = new WebFactory($globals);
        $this->response = $factory->newResponse();
        $this->headers = $this->response->headers;
    }
    
    protected function assertHeaders(array $expect)
    {
        $actual = $this->headers->get();
        $this->assertSame($expect, $actual);
    }
    
    public function test__get()
    {
        $this->assertInstanceOf('Aura\Web\Response\Status',   $this->response->status);
        $this->assertInstanceOf('Aura\Web\Response\Headers',  $this->response->headers);
        $this->assertInstanceOf('Aura\Web\Response\Cookies',  $this->response->cookies);
        $this->assertInstanceOf('Aura\Web\Response\Content',  $this->response->content);
        $this->assertInstanceOf('Aura\Web\Response\Cache',    $this->response->cache);
    }
    
    public function testRedirect()
    {
        $this->response->redirect('http://example.com');
        $this->assertSame(302, $this->response->status->getCode());
        $this->assertSame('Found', $this->response->status->getPhrase());
        $this->assertHeaders(array(
            'Location' => 'http://example.com',
        ));
    }
    
    public function testRedirectNoCache()
    {
        $this->response->redirectNoCache('http://example.com');
        $this->assertSame(303, $this->response->status->getCode());
        $this->assertSame('See Other', $this->response->status->getPhrase());
        $this->assertHeaders(array(
            'Location' => 'http://example.com',
            'Cache-Control' => 'no-cache, no-store, must-revalidate, proxy-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Mon, 01 Jan 0001 00:00:00 GMT',
        ));
    }
}
