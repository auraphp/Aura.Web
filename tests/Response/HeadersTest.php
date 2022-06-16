<?php
namespace Aura\Web\Response;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class HeadersTest extends TestCase
{
    protected $headers;

    protected function set_up()
    {
        $this->headers = new Headers;
    }

    public function testSetAndGet()
    {
        $this->headers->set('foo-bar', 'baz');
        $this->headers->set('dib', 'zim');

        // get one
        $expect = 'baz';
        $actual = $this->headers->get('foo-bar');
        $this->assertSame($expect, $actual);

        // get all
        $expect = array(
            'Foo-Bar' => 'baz',
            'Dib' => 'zim',
        );
        $actual = $this->headers->get();
        $this->assertSame($expect, $actual);

        // no such header
        $this->assertNull($this->headers->get('no-such-header'));
    }

    public function testAddAndGet()
    {
        $this->headers->add('foo-bar', 'baz');
        $this->headers->add('dib', 'zim');
        $this->headers->add('dib', 'zam');

        // get one
        $expect = 'baz';
        $actual = $this->headers->get('foo-bar');
        $this->assertSame($expect, $actual);

        // get all
        $expect = array(
          'Foo-Bar' => 'baz',
          'Dib' => array('zim', 'zam'),
        );
        $actual = $this->headers->get();
        $this->assertSame($expect, $actual);
    }
}
