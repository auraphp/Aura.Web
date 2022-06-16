<?php
namespace Aura\Web;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class ResponseTest extends TestCase
{
    protected $response;

    protected $headers;

    protected function set_up()
    {
        parent::set_up();
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
}
