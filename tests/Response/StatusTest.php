<?php
namespace Aura\Web\Response;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class StatusTest extends TestCase
{
    protected $status;

    protected function set_up()
    {
        $this->status = new Status;
    }

    public function testSetAndGet()
    {
        $this->status->set(404, 'Not Found', '1.0');
        $actual = $this->status->get();
        $expect = 'HTTP/1.0 404 Not Found';
        $this->assertSame($expect, $actual);
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
        $this->expectException('Aura\Web\Exception\InvalidStatusCode');
        $this->status->setCode('88');
    }

    public function testSetAndGetPhrase()
    {
        $expect = 'Not Found';
        $this->status->setPhrase($expect);
        $actual = $this->status->getPhrase();
        $this->assertSame($expect, $actual);
    }

    /**
     *
     * Check all the possible HTTP protocol versions.
     *
     * @dataProvider provideSupportedVersions
     *
     * @param string $version The HTTP protocol version to test.
     *
     */
    public function testSetAndGetVersion($version)
    {
        $this->status->setVersion($version);
        $actual = $this->status->getVersion();
        $this->assertSame($version, $actual);
    }

    public function provideSupportedVersions()
    {
        return array(
            array('1.1'),
            array('1.0'),
            array('2'),
        );
    }

    public function testVersionWrong()
    {
        $this->expectException('Aura\Web\Exception\InvalidVersion');
        $this->status->setVersion('88');
    }
}
