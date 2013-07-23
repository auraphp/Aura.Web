<?php
namespace Aura\Web;

class PageTest extends \PHPUnit_Framework_TestCase
{
    protected $page;

    protected function setUp()
    {
        parent::setUp();
    }
    
    protected function newPage($params = null)
    {
        return new MockPage(
            new Context($GLOBALS),
            new Accept($_SERVER),
            new Response,
            new Signal,
            new Renderer\None,
            $params
        );
    }
    
    protected function tearDown()
    {
        parent::tearDown();
    }
    
    public function testGetters()
    {
        $params = ['action' => 'test', 'format' => '.test'];
        $page = $this->newPage($params);
        $this->assertInstanceOf('Aura\Web\Accept', $page->getAccept());
        $this->assertInstanceOf('Aura\Web\Context', $page->getContext());
        $this->assertInstanceOf('Aura\Web\Response', $page->getResponse());
        $this->assertInstanceOf('Aura\Web\Signal', $page->getSignal());
        $this->assertSame($params, $page->getParams());
        $this->assertSame('test', $page->getAction());
        $this->assertSame('.test', $page->getFormat());
        
    }
    
    public function testExecAndHooks()
    {
        $page = $this->newPage(['action' => 'index']);
        $response = $page->exec();
        $this->assertInstanceOf('Aura\Web\Response', $response);
        
        $data = $page->getData();
        $this->assertSame('actionIndex', $data->action_method);
        
        $expect = [
            'preExec' => true,
            'preAction' => true,
            'postAction' => true,
            'preRender' => true,
            'postRender' => true,
            'postExec' => true,
        ];
        
        $this->assertSame($expect, $page->hooks);
    }

    public function testExecNoMethodForAction()
    {
        $page = $this->newPage(['action' => 'noSuchAction']);
        $this->setExpectedException('Aura\Web\Exception\NoMethodForAction');
        $response = $page->exec();
    }

    public function testExecAndParams()
    {
        $page = $this->newPage([
            'action' => 'params',
            'foo' => 'zim',
        ]);
        
        $response = $page->exec();
        
        $expect = [
          'foo' => 'zim',
          'bar' => NULL,
          'baz' => 'dib',
        ];
        
        $actual = (array) $page->getData();
        
        $this->assertSame($expect, $actual);
    }
}
