<?php
namespace Aura\Web\Response;

class RenderTest extends \PHPUnit_Framework_TestCase
{
    protected $render;
    
    protected function setUp()
    {
        $this->render = new Render;
    }
    
    public function test()
    {
        $this->render->view = 'bar';
        $this->assertSame('bar', $this->render->view);
    }
}
