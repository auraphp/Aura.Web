<?php
namespace Aura\Web;

use Aura\Web\Response\PropertyFactory;
use Aura\Web\AssertHeadersTrait;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    use AssertHeadersTrait;
    
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
    
    public function testGetTransfer()
    {
        $this->response->content->set(function () { return 'foo bar baz'; });
        $this->response->content->setCharset('utf-8');
        $this->response->content->setType('text/plain');
        $this->response->content->setDisposition('attachment', 'filename.txt');
        $this->response->redirectNoCache('http://example.com');
        
        $expect = (object) array(
            'status' => array(
                'version' => 1.1,
                'code' => 303,
                'phrase' => 'See Other',
            ),
            'headers' => array(
                'Content-Disposition' => 'attachment; filename="filename.txt"',
                'Content-Type' => 'text/plain; charset=utf-8',
                'Location' => 'http://example.com',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'no-cache, no-store, must-revalidate, proxy-revalidate',
                'Expires' => 'Mon, 01 Jan 0001 00:00:00 GMT',
            ),
            'cookies' => array(
            ),
            'content' => 'foo bar baz',
        );
        
        $actual = $this->response->getTransfer();
        
        $this->assertEquals($expect, $actual);
    }
}
