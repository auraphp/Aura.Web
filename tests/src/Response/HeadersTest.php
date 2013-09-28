<?php
namespace Aura\Web\Response;

class HeadersTest extends \PHPUnit_Framework_TestCase
{
    protected $headers;
    
    protected function setUp()
    {
        $this->headers = new Headers;
    }

    public function testSet()
    {
        $this->headers->set('foo-bar', 'baz');
        $this->headers->set('dib', 'zim');
        
        $expect = array(
            'Foo-Bar' => 'baz',
            'Dib' => 'zim',
        );
        
        $actual = $this->headers->get();
        
        $this->assertSame($expect, $actual);
    }

    public function testAdd()
    {
        $this->headers->add('foo', 'bar');
        $this->headers->add('foo', 'baz');
        $this->headers->add('foo', 'dib');
        
        $expect = array(
            'Foo' => array('bar', 'baz', 'dib'),
        );
        
        $actual = $this->headers->get();
        
        $this->assertSame($expect, $actual);
    }

    public function testGet()
    {
        $this->headers->set('foo-bar', 'baz');
        $this->headers->add('dib', 'zim');
        $this->headers->add('dib', 'gir');
        
        $expect = 'baz';
        $actual = $this->headers->get('foo-bar');
        $this->assertSame($expect, $actual);
        
        $expect = array('zim', 'gir');
        $actual = $this->headers->get('dib');
        $this->assertSame($expect, $actual);
        
        // no such header
        $this->assertNull($this->headers->get('no-such-header'));
    }

    public function testGetAll()
    {
        $this->headers->set('foo-bar', 'baz');
        $this->headers->add('dib', 'zim');
        $this->headers->add('dib', 'gir');
        
        $expect = array(
            'Foo-Bar' => 'baz',
            'Dib' => array('zim', 'gir'),
        );
        $actual = $this->headers->get();
        $this->assertSame($expect, $actual);
    }

}
