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
        $expire = time() + 3600;
        $this->cookies->set('foo', 'bar', $expire, '/path', 'example.com');
        
        $expect = array(
          'value' => 'bar',
          'expire' => $expire,
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
        $expire = time() + 3600;
        $this->cookies->set('foo', 'bar', $expire, '/path', 'example.com');
        $this->cookies->set('baz', 'dib', date('Y-m-d H:i:s', $expire), '/path', 'example.com');
        
        $expect = array(
            'foo' => array(
              'value' => 'bar',
              'expire' => $expire,
              'path' => '/path',
              'domain' => 'example.com',
              'secure' => false,
              'httponly' => true,
            ),
            'baz' => array(
              'value' => 'dib',
              'expire' => $expire,
              'path' => '/path',
              'domain' => 'example.com',
              'secure' => false,
              'httponly' => true,
            ),
        );

        $actual = $this->cookies->get();
        
        $this->assertSame($expect, $actual);
    }

    public function testHttponly()
    {
        $this->cookies->setHttponly(false);
        $this->assertFalse($this->cookies->getHttponly());
        
        $expire = time() + 3600;
        $this->cookies->set('foo', 'bar', $expire, '/path', 'example.com');
        
        $expect = array(
          'value' => 'bar',
          'expire' => $expire,
          'path' => '/path',
          'domain' => 'example.com',
          'secure' => false,
          'httponly' => false,
        );

        $actual = $this->cookies->get('foo');
        
        $this->assertSame($expect, $actual);
    }

}
