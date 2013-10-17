<?php
namespace Aura\Web;

use Aura\Web\Request\PropertyFactory;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function test__get()
    {
        $globals = array(
            '_SERVER' => array(
                'HTTP_CONTENT_TYPE' => 'text/html',
                'HTTP_X_JSON' => 'delete-me',
            )
        );
        
        $factory = new WebFactory($globals);
        
        $request = $factory->newRequest();
        $this->assertNotNull($request->cookies);
        $this->assertNotNull($request->env);
        $this->assertNotNull($request->files);
        $this->assertNotNull($request->headers);
        $this->assertNotNull($request->content);
        $this->assertNotNull($request->method);
        $this->assertNotNull($request->negotiate);
        $this->assertNotNull($request->post);
        $this->assertNotNull($request->query);
        $this->assertNotNull($request->server);
        $this->assertNotNull($request->url);
    }
}
