<?php
namespace Aura\Web\Request;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class ParamsTest extends TestCase
{
    public function test()
    {
        $params = new Params;
        $params->set(array('foo' => 'bar'));

        $actual = $params->get('foo');
        $this->assertSame('bar', $actual);

        $actual = $params->get('baz');
        $this->assertNull($actual);

        // return alt
        $actual = $params->get('baz', 'dib');
        $this->assertSame('dib', $actual);

        // return all
        $actual = $params->get();
        $this->assertSame(array('foo' => 'bar'), $actual);
    }
}
