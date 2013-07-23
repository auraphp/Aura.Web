<?php
namespace Aura\Web\Request;

class FilesTest extends \PHPUnit_Framework_TestCase
{
    public function newFiles($filedata = [])
    {
        return new Files($filedata);
    }
    
    public function testGetFiles()
    {
        // single file
        $filedata['foo'] = [
            'error'     => null,
            'name'      => 'bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        ];
        // bar[]
        $filedata['bar'] = [
            'error'     => [null, null],
            'name'      => ['foo', 'fooz'],
            'size'      => [null, null],
            'tmp_name'  => [null, null],
            'type'      => [null, null],
        ];
        // upload[file1]
        $filedata['upload']['file1'] = [
            'error'     => null,
            'name'      => 'file1.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        ];
        $filedata['upload']['file2'] = [
            'error'     => null,
            'name'      => 'file2.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        ];
        
        $files = $this->newFiles($filedata);
        
        $actual = $files->get('foo');
        $this->assertSame('bar', $actual['name']);
        
        $actual = $files->get('bar');
        $this->assertSame('foo',  $actual[0]['name']);
        $this->assertSame('fooz', $actual[1]['name']);
        
        $actual = $files->get('upload');
        $this->assertSame('file1.bar', $actual['file1']['name']);
        $this->assertSame('file2.bar', $actual['file2']['name']);
        
        $actual = $files->get('baz');
        $this->assertNull($actual);
        
        // return default
        $actual = $files->get('baz', 'dib');
        $this->assertSame('dib', $actual);
    }
}
