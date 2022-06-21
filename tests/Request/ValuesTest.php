<?php
namespace Aura\Web\Request;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class ValuesTest extends TestCase
{
    public function testGet()
    {
        $values = new Values(array('foo' => 'bar'));

        $actual = $values->get('foo');
        $this->assertSame('bar', $actual);

        $actual = $values->get('baz');
        $this->assertNull($actual);

        // return alt
        $actual = $values->get('baz', 'dib');
        $this->assertSame('dib', $actual);

        // return all
        $actual = $values->get();
        $this->assertSame(array('foo' => 'bar'), $actual);
    }

    public function testGetDottedNotation()
    {
        $nested_array = array(
            'foo' => array(
                'foo' => 'bar',
                'bar' => array('foo' => 'deeply nested'),
                'foo.with.dot' => 'value'
            ),
            'foo.with.dot' => 'string value'
        );

        $values = new Values($nested_array);

        // normal behavior
        $this->assertSame($nested_array['foo'], $values->get('foo'));

        // access dotted key that is in original array (backwards compatible)
        $this->assertSame('string value', $values->get('foo.with.dot'));

        // access foo[foo] using dot notation
        $this->assertSame('bar', $values->get('foo.foo'));

        // access dotted key within array
        $this->assertSame('value', $values->get('foo.foo.with.dot'));

        // access non existing key in foo
        $this->assertNull($values->get('foo.missing'));

        // fallback to default for missing key in foo
        $this->assertSame('default', $values->get('foo.missing', 'default'));

        // access foo[bar][foo] using dot notation
        $this->assertSame('deeply nested', $values->get('foo.bar.foo'));

        // access missing key in foo[bar] array
        $this->assertNull($values->get('foo.bar.missing'));

        // fallback to default while accessing missing key in foo[bar]
        $this->assertSame('def', $values->get('foo.bar.missing', 'def'));
    }

    public function testGetBool()
    {
        $values = new Values(array(
            'truthy' => 'y',
            'falsy' => 'off',
            'neither' => 'doom',
        ));

        $this->assertTrue($values->getBool('truthy'));
        $this->assertFalse($values->getBool('falsy'));
        $this->assertNull($values->getBool('neither'));
        $this->assertNull($values->getBool('missing'));
    }

    public function testGetBoolWithDottedNotation()
    {
        $values = new Values(array(
            'foo' => array(
                'truthful' => '1',
                'untruthfully' => 'false',
                'array' => array(),
                'bar' => 'string',
            )
        ));

        $this->assertNull($values->getBool('foo'));
        $this->assertTrue($values->getBool('foo.truthful'));
        $this->assertFalse($values->getBool('foo.untruthfully'));
        $this->assertNull($values->getBool('foo.array'));
        $this->assertNull($values->getBool('foo.bar'));
    }

    public function testGetInt()
    {
        $values = new Values(array(
            'int' => '88',
            'float' => '12.34',
            'string' => 'doom',
        ));

        $this->assertSame(88, $values->getInt('int'));
        $this->assertSame(12, $values->getInt('float'));
        $this->assertSame(0, $values->getInt('string'));
        $this->assertNull($values->getInt('missing'));
    }

    public function testGetIntWithDottedNotation()
    {
        $values = new Values(array(
            'foo' => array(
                'string' => 'doom',
                'integer' => '1',
                'float' => '2.1',
                'array' => array(),
            )
        ));

        $this->assertNull($values->getInt('foo'));
        $this->assertSame(0, $values->getInt('foo.string'));
        $this->assertSame(1, $values->getInt('foo.integer'));
        $this->assertSame(2, $values->getInt('foo.float'));
        $this->assertNull($values->getInt('foo.array'));
    }

    public function testGetFloat()
    {
        $values = new Values(array(
            'int' => '88',
            'float' => '12.34',
            'string' => 'doom',
        ));

        $this->assertSame(88.0, $values->getFloat('int'));
        $this->assertSame(12.34, $values->getFloat('float'));
        $this->assertSame(0.0, $values->getFloat('string'));
        $this->assertNull($values->getFloat('missing'));
    }

    public function testGetFloatWithDottedNotation()
    {
        $values = new Values(array(
            'foo' => array(
                'string' => 'doom',
                'integer' => '1',
                'float' => '2.1',
                'array' => array(),
            )
        ));

        $this->assertNull($values->getFloat('foo'));
        $this->assertSame(0.0, $values->getFloat('foo.string'));
        $this->assertSame(1.0, $values->getFloat('foo.integer'));
        $this->assertSame(2.1, $values->getFloat('foo.float'));
        $this->assertNull($values->getFloat('foo.array'));
    }
}
