<?php
namespace Aura\Web;

use Aura\Web\Response\PropertyFactory;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    protected $response;

    protected function setUp()
    {
        parent::setUp();
        $this->response = new Response(new PropertyFactory);
    }
    
    public function test__get()
    {
        $this->assertInstanceOf('Aura\Web\Response\Content',  $this->response->content);
        $this->assertInstanceOf('Aura\Web\Response\Cookies',  $this->response->cookies);
        $this->assertInstanceOf('Aura\Web\Response\Headers',  $this->response->headers);
        $this->assertInstanceOf('Aura\Web\Response\Redirect', $this->response->redirect);
        $this->assertInstanceOf('Aura\Web\Response\Render',   $this->response->render);
        $this->assertInstanceOf('Aura\Web\Response\Status',   $this->response->status);
    }
    
    public function testGetTransfer()
    {
        $this->response->content->set('foo bar baz');
        $this->response->content->setCharset('utf-8');
        $this->response->content->setType('text/plain');
        $this->response->content->setDisposition('attachment', 'filename.txt');
        $this->response->redirect->withoutCache('http://example.com');
        
        $expect = (object) array(
            'status' => array(
                'version' => 1.1,
                'code' => 303,
                'phrase' => 'See Other',
            ),
            'headers' => array(
                'Content-Disposition' => 'attachment; filename=filename.txt',
                'Content-Type' => 'text/plain; charset=utf-8',
                'Location' => 'http://example.com',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                'Expires' => '1',
            ),
            'cookies' => array(
            ),
            'content' => 'foo bar baz',
        );
        
        $actual = $this->response->getTransfer();
        
        $this->assertEquals($expect, $actual);
    }
}
