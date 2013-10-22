<?php
namespace Aura\Web\Response;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    protected $status;
    
    protected function setUp()
    {
        $this->status = new Status;
    }
    
    public function testSetAndGet()
    {
        $this->status->set(404, 'Not Found', 1.0);
        $this->assertSame(404, $this->status->getCode());
        $this->assertSame('Not Found', $this->status->getPhrase());
        $this->assertSame(1.0, $this->status->getVersion());
        
        $expect = array(
            'version' => 1.0,
            'code'    => 404,
            'phrase'  => 'Not Found',
        );
        
        $this->assertEquals($expect, $this->status->get());
    }
    
    public function testSetAndGetCode()
    {
        $expect = 404;
        $this->status->setCode($expect);
        $actual = $this->status->getCode();
        $this->assertSame($expect, $actual);
        
        $this->status->setCode('555');
        $this->assertSame('', $this->status->getPhrase());
    }
    
    public function testSetCodeWrong()
    {
        $this->setExpectedException('Aura\Web\Exception\InvalidStatusCode');
        $this->status->setCode('88');
    }
    
    public function testSetAndGetPhrase()
    {
        $expect = 'Not Found';
        $this->status->setPhrase($expect);
        $actual = $this->status->getPhrase();
        $this->assertSame($expect, $actual);
    }

    public function testSetAndGetVersion()
    {
        $expect = 1.1;
        $this->status->setVersion($expect);
        $actual = $this->status->getVersion();
        $this->assertSame($expect, $actual);
    }

    public function testVersionWrong()
    {
        $this->setExpectedException('Aura\Web\Exception\InvalidVersion');
        $this->status->setVersion('88');
    }
}
