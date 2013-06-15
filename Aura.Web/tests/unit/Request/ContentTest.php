<?php
namespace Aura\Web\Request;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        stream_wrapper_unregister('php');
        stream_wrapper_register('php', 'Aura\Web\PhpStream');
    }
    
    public function tearDown()
    {
        stream_wrapper_restore('php');
    }
    
    public function testGetInput()
    {
        $this->reset();
        
        $object = (object) [
            'foo' => 'bar',
            'baz' => 'dib',
            'zim' => 'gir',
        ];
        
        $encode = json_encode($object);
        
        PhpStream::$content = $encode;
        
        $context = $this->newContext();
        
        $this->assertSame($encode, $context->getInput());
    }
    
    public function testGetJsonInput()
    {
        $this->reset();
        
        $object = (object) [
            'foo' => 'bar',
            'baz' => 'dib',
            'zim' => 'gir',
        ];
        
        $encode = json_encode($object);
        
        PhpStream::$content = $encode;
        
        $context = $this->newContext();
        
        $this->assertEquals($object, $context->getJsonInput());
    }
    
}
