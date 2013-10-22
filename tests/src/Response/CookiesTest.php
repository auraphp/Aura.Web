<?php
namespace Aura\Web\Response;

class CookiesTest extends \PHPUnit_Framework_TestCase
{
    protected $cookies;
    
    protected function setUp()
    {
        $this->cookies = new Cookies;
    }

    public function testSetAndGet()
    {
        $this->cookies->set('foo', 'bar', '88', '/path', 'example.com');
        
        $expect = array(
          'value' => 'bar',
          'expire' => 88,
          'path' => '/path',
          'domain' => 'example.com',
          'secure' => false,
          'httponly' => true,
        );

        $actual = $this->cookies->get('foo');
        
        $this->assertSame($expect, $actual);
    }

    public function testGetAll()
    {
        $this->cookies->set('foo', 'bar', '88', '/path', 'example.com');
        $this->cookies->set('baz', 'dib', date('Y-m-d H:i:s', '88'), '/path', 'example.com');
        
        $expect = array(
            'foo' => array(
              'value' => 'bar',
              'expire' => 88,
              'path' => '/path',
              'domain' => 'example.com',
              'secure' => false,
              'httponly' => true,
            ),
            'baz' => array(
              'value' => 'dib',
              'expire' => 88,
              'path' => '/path',
              'domain' => 'example.com',
              'secure' => false,
              'httponly' => true,
            ),
        );

        $actual = $this->cookies->get();
        
        $this->assertSame($expect, $actual);
    }

    public function testGetDefault()
    {
        $this->cookies->setHttponly(false);
        $actual = $this->cookies->getDefault();
        $this->assertFalse($actual['httponly']);
        
        $this->cookies->set('foo', 'bar', '88', '/path', 'example.com');
        
        $expect = array(
          'value' => 'bar',
          'expire' => 88,
          'path' => '/path',
          'domain' => 'example.com',
          'secure' => false,
          'httponly' => false,
        );

        $actual = $this->cookies->get('foo');
        
        $this->assertSame($expect, $actual);
    }

}
