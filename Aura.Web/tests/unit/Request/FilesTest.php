<?php
namespace Aura\Web\Request;

class FilesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFiles()
    {
        $this->reset();
        // single file
        $_FILES['foo'] = [
            'error'     => null,
            'name'      => 'bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        ];
        // bar[]
        $_FILES['bar'] = [
            'error'     => [null, null],
            'name'      => ['foo', 'fooz'],
            'size'      => [null, null],
            'tmp_name'  => [null, null],
            'type'      => [null, null],
        ];
        // upload[file1]
        $_FILES['upload']['file1'] = [
            'error'     => null,
            'name'      => 'file1.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        ];
        $_FILES['upload']['file2'] = [
            'error'     => null,
            'name'      => 'file2.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        ];
        
        $context = $this->newContext();
        
        $actual = $context->getFiles('foo');
        $this->assertSame('bar', $actual['name']);
        
        $actual = $context->getFiles('bar');
        $this->assertSame('foo',  $actual[0]['name']);
        $this->assertSame('fooz', $actual[1]['name']);
        
        $actual = $context->getFiles('upload');
        $this->assertSame('file1.bar', $actual['file1']['name']);
        $this->assertSame('file2.bar', $actual['file2']['name']);
        
        $actual = $context->getFiles('baz');
        $this->assertNull($actual);
        
        // return default
        $actual = $context->getFiles('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $this->reset();
        $_FILES['foo'] = [
            'error'     => null,
            'name'      => 'bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        ];
        
        $context    = $this->newContext();
        $actual = $context->getFiles();
        $this->assertSame($_FILES, $actual);
    }

}
