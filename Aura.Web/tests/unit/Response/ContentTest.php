<?php
namespace Aura\Web\Response;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    protected $content;
    
    protected function setUp()
    {
        $this->content = new Content;
    }
    
    public function testContent()
    {
        $content = 'foo bar baz';
        $this->content->set($content);
        $this->assertSame($content, $this->content->get());
    }

    public function testType()
    {
        $expect = 'application/json';
        $this->content->setType($expect);
        $actual = $this->content->getType();
        $this->assertSame($expect, $actual);
    }

    public function testCharset()
    {
        $expect = 'utf-8';
        $this->content->setCharset($expect);
        $actual = $this->content->getCharset();
        $this->assertSame($expect, $actual);
    }

    public function testDisposition()
    {
        $disposition = 'attachment';
        $filename = 'example.txt';
        $this->content->setDisposition($disposition, $filename);
        $this->assertSame($disposition, $this->content->getDisposition());
        $this->assertSame($filename, $this->content->getFilename());
    }
}
