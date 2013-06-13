<?php
namespace Aura\Web\Request;

class NegotiateTest extends \PHPUnit_Framework_TestCase
{
    protected function newNegotiate($server = [])
    {
        return new Negotiate($server);
    }
    
    public function testGetAccept()
    {
        $negotiate = $this->newNegotiate([
            'HTTP_ACCEPT' => 'text/*;q=0.9, text/html, text/xhtml;q=0.8',
        ]);
        
        $expect = [
            'text/html'  => 1.0,
            'text/*'     => 0.9,
            'text/xhtml' => 0.8,
        ];
        
        $actual = $negotiate->getAccept();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testGetAcceptCharset()
    {
        $negotiate = $this->newNegotiate([
            'HTTP_ACCEPT_CHARSET' => 'iso-8859-5, unicode-1-1;q=0.8',
        ]);
        
        $expect = [
            'ISO-8859-1'  => 1.0,
            'iso-8859-5'  => 1.0,
            'unicode-1-1' => 0.8,
        ];
        
        $actual = $negotiate->getAcceptCharset();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testGetAcceptEncoding()
    {
        $negotiate = $this->newNegotiate([
            'HTTP_ACCEPT_ENCODING' => 'compress;q=0.5, gzip;q=1.0',
        ]);
        
        $expect = [
            'gzip'     => 1.0,
            'compress' => 0.5,
        ];
        
        $actual = $negotiate->getAcceptEncoding();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testGetAcceptLanguage()
    {
        $negotiate = $this->newNegotiate([
            'HTTP_ACCEPT_LANGUAGE' => 'en-US',
        ]);
        $expect = ['en-US' => 1.0];
        $actual = $negotiate->getAcceptLanguage();
        $this->assertSame($expect, $actual);
    }
    
    public function testGetMedia()
    {
        $negotiate = $this->newNegotiate([
            'HTTP_ACCEPT' => 'application/json, application/xml, text/*, */*',
        ]);
        
        // nothing available
        $expect = false;
        $actual = $negotiate->getMedia([]);
        $this->assertSame($expect, $actual);
        
        // explicitly accepts */*, and no matching media are available
        $expect = 'foo/bar';
        $actual = $negotiate->getMedia(['foo/bar', 'baz/dib']);
        $this->assertSame($expect, $actual);
        
        // explictly accepts application/xml, which is explictly available.
        // note that it returns the *available* value, which is determined
        // by the developer, not the acceptable value, which is determined
        // by the user/client/headers.
        $expect = 'application/XML';
        $actual = $negotiate->getMedia(['application/XML', 'text/csv']);
        $this->assertSame($expect, $actual);
        
        // a subtype is available
        $expect = 'text/csv';
        $actual = $negotiate->getMedia(['foo/bar', 'text/csv', 'baz/qux']);
        $this->assertSame($expect, $actual);
        
        // no acceptable media specified, use first available
        $negotiate = $this->newNegotiate();
        $expect = 'application/json';
        $actual = $negotiate->getMedia(['application/json', 'application/xml']);
        $this->assertSame($expect, $actual);
        
        // media is available but quality level is not acceptable
        $negotiate = $this->newNegotiate([
            'HTTP_ACCEPT' => 'application/json, application/xml, text/*, foo/bar;q=0',
        ]);
        $expect = false;
        $actual = $negotiate->getMedia(['foo/bar']);
        $this->assertSame($expect, $actual);
    }
}
