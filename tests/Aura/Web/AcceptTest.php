<?php
namespace Aura\Web;

class AcceptTest extends \PHPUnit_Framework_TestCase
{
    protected $accept;
    
    protected function setUp()
    {
        $_SERVER['HTTP_ACCEPT'] = 'text/*;q=0.9, text/html ,text/xhtml;q=0.8';
        $_SERVER['HTTP_ACCEPT_CHARSET'] = 'iso-8859-5, unicode-1-1;q=0.8';
        $_SERVER['HTTP_ACCEPT_ENCODING'] = 'compress;q=0.5, gzip;q=1.0';
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US';
        $this->accept = new Accept($_SERVER);
    }
    
    public function testGetContentType()
    {
        $expect = [
            'text/html'  => 1.0,
            'text/*'     => 0.9,
            'text/xhtml' => 0.8,
        ];
        $actual = $this->accept->getContentType();
        $this->assertSame($expect, $actual);
    }
    
    public function testGetCharset()
    {
        $expect = [
            'iso-8859-5'  => 1.0,
            'unicode-1-1' => 0.8,
        ];
        $actual = $this->accept->getCharset();
        $this->assertSame($expect, $actual);
    }
    
    public function testGetEncoding()
    {
        $expect = [
            'gzip'     => 1.0,
            'compress' => 0.5,
        ];
        $actual = $this->accept->getEncoding();
        $this->assertSame($expect, $actual);
    }
    
    public function testGetLanguage()
    {
        $expect = ['en-US' => 1.0];
        $actual = $this->accept->getLanguage();
        $this->assertSame($expect, $actual);
    }
}
