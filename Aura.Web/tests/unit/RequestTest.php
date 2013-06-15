<?php
namespace Aura\Web;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected function newRequest(array $agents = [])
    {
        return new Context($GLOBALS, $agents);
    }
    
    public function test__get()
    {
        $this->reset();
        $context = $this->newContext();
        
        // test that we can access without causing an exception
        $context->get;
        $context->post;
        $context->server;
        $context->cookie;
        $context->env;
        $context->files;
        $context->header;
        
        // invalid or protected should cause an exception
        $this->setExpectedException('\UnexpectedValueException');
        $context->invalid;
    }
}
