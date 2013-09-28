<?php
namespace Aura\Web\Request;

class NegotiateTest extends \PHPUnit_Framework_TestCase
{
    protected function newNegotiate($server = array())
    {
        return new Negotiate($server);
    }
    
    public function testGet()
    {
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT' => 'text/*;q=0.9, text/html, text/xhtml;q=0.8',
            'HTTP_ACCEPT_CHARSET' => 'iso-8859-5, unicode-1-1;q=0.8',
            'HTTP_ACCEPT_ENCODING' => 'compress;q=0.5, gzip;q=1.0',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US, en-GB, en, *',
        ));
        
        $available = array(
            'charset'  => array('unicode-1-1'),
            'encoding' => array(),
            'language' => array('en-us'),
            'media'    => array('text/html'),
        );
        
        $expect = array(
            'charset'  => 'unicode-1-1',
            'encoding' => false,
            'language' => 'en-us',
            'media'    => 'text/html',
        );
        
        $actual = $negotiate->get($available);
        $this->assertSame($expect, $actual);
    }
    
    public function testGetAccept()
    {
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT' => 'text/*;q=0.9, text/html, text/xhtml;q=0.8',
        ));
        
        $expect = array(
            'text/html'  => 1.0,
            'text/*'     => 0.9,
            'text/xhtml' => 0.8,
        );
        
        $actual = $negotiate->getAccept();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testGetAcceptCharset()
    {
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT_CHARSET' => 'iso-8859-5, unicode-1-1;q=0.8',
        ));
        
        $expect = array(
            'ISO-8859-1'  => 1.0,
            'iso-8859-5'  => 1.0,
            'unicode-1-1' => 0.8,
        );
        
        $actual = $negotiate->getAcceptCharset();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testGetAcceptEncoding()
    {
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT_ENCODING' => 'compress;q=0.5, gzip;q=1.0',
        ));
        
        $expect = array(
            'gzip'     => 1.0,
            'compress' => 0.5,
        );
        
        $actual = $negotiate->getAcceptEncoding();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testGetAcceptLanguage()
    {
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT_LANGUAGE' => 'en-US, en-GB, en, *',
        ));
        
        $expect = array(
            'en-US' => 1.0,
            'en-GB' => 1.0,
            'en' => 1.0,
            '*' => 1.0
        );
        
        $actual = $negotiate->getAcceptLanguage();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testGetCharset()
    {
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT_CHARSET' => 'iso-8859-5, unicode-1-1, *',
        ));
        
        // nothing available
        $expect = false;
        $actual = $negotiate->getCharset(array());
        $this->assertSame($expect, $actual);
        
        // explicitly accepts *, and no matching charset available
        $expect = 'foo';
        $actual = $negotiate->getCharset(array('foo', 'bar'));
        $this->assertSame($expect, $actual);
        
        // explictly accepts unicode-1-1, which is explictly available.
        // note that it returns the *available* value, which is determined
        // by the developer, not the acceptable value, which is determined
        // by the user/client/headers.
        $expect = 'UniCode-1-1';
        $actual = $negotiate->getCharset(array('foo', 'UniCode-1-1'));
        $this->assertSame($expect, $actual);
        
        // no acceptable charset specified, use first available
        $negotiate = $this->newNegotiate();
        $expect = 'ISO-8859-5';
        $actual = $negotiate->getCharset(array('ISO-8859-5', 'foo'));
        $this->assertSame($expect, $actual);
        
        // charset is available but quality level is not acceptable
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1, baz;q=0',
        ));
        $expect = false;
        $actual = $negotiate->getCharset(array('baz'));
        $this->assertSame($expect, $actual);
    }
    
    public function testGetEncoding()
    {
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT_ENCODING' => 'gzip, compress, *',
        ));
        
        // nothing available
        $expect = false;
        $actual = $negotiate->getEncoding(array());
        $this->assertSame($expect, $actual);
        
        // explicitly accepts *, and no matching encoding available
        $expect = 'foo';
        $actual = $negotiate->getEncoding(array('foo', 'bar'));
        $this->assertSame($expect, $actual);
        
        // explictly accepts compress, which is explictly available.
        // note that it returns the *available* value, which is determined
        // by the developer, not the acceptable value, which is determined
        // by the user/client/headers.
        $expect = 'GZIP';
        $actual = $negotiate->getEncoding(array('foo', 'GZIP'));
        $this->assertSame($expect, $actual);
        
        // no acceptable encoding specified, use first available
        $negotiate = $this->newNegotiate();
        $expect = 'gzip';
        $actual = $negotiate->getEncoding(array('gzip', 'compress'));
        $this->assertSame($expect, $actual);
        
        // encoding is available but quality level is not acceptable
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT_ENCODING' => 'gzip, compress, foo;q=0',
        ));
        $expect = false;
        $actual = $negotiate->getEncoding(array('foo'));
        $this->assertSame($expect, $actual);
    }
    
    public function testGetLanguage()
    {
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT_LANGUAGE' => 'en-US, en-GB, en, *',
        ));
        
        // nothing available
        $expect = false;
        $actual = $negotiate->getLanguage(array());
        $this->assertSame($expect, $actual);
        
        // explicitly accepts *, and no matching language available
        $expect = 'foo-bar';
        $actual = $negotiate->getLanguage(array('foo-bar', 'baz-dib'));
        $this->assertSame($expect, $actual);
        
        // explictly accepts en-gb, which is explictly available.
        // note that it returns the *available* value, which is determined
        // by the developer, not the acceptable value, which is determined
        // by the user/client/headers.
        $expect = 'en-gb';
        $actual = $negotiate->getLanguage(array('en-gb', 'fr-FR'));
        $this->assertSame($expect, $actual);
        
        // a subtype is available
        $expect = 'en-zo';
        $actual = $negotiate->getLanguage(array('foo-bar', 'en-zo', 'baz-qux'));
        $this->assertSame($expect, $actual);
        
        // no acceptable language specified, use first available
        $negotiate = $this->newNegotiate();
        $expect = 'en-us';
        $actual = $negotiate->getLanguage(array('en-us', 'en-gb'));
        $this->assertSame($expect, $actual);
        
        // language is available but quality level is not acceptable
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT_LANGUAGE' => 'en-us, en-gb, en, foo-bar;q=0',
        ));
        $expect = false;
        $actual = $negotiate->getLanguage(array('foo-bar'));
        $this->assertSame($expect, $actual);
    }
    
    public function testGetMedia()
    {
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT' => 'application/json, application/xml, text/*, */*',
        ));
        
        // nothing available
        $expect = false;
        $actual = $negotiate->getMedia(array());
        $this->assertSame($expect, $actual);
        
        // explicitly accepts */*, and no matching media are available
        $expect = 'foo/bar';
        $actual = $negotiate->getMedia(array('foo/bar', 'baz/dib'));
        $this->assertSame($expect, $actual);
        
        // explictly accepts application/xml, which is explictly available.
        // note that it returns the *available* value, which is determined
        // by the developer, not the acceptable value, which is determined
        // by the user/client/headers.
        $expect = 'application/XML';
        $actual = $negotiate->getMedia(array('application/XML', 'text/csv'));
        $this->assertSame($expect, $actual);
        
        // a subtype is available
        $expect = 'text/csv';
        $actual = $negotiate->getMedia(array('foo/bar', 'text/csv', 'baz/qux'));
        $this->assertSame($expect, $actual);
        
        // no acceptable media specified, use first available
        $negotiate = $this->newNegotiate();
        $expect = 'application/json';
        $actual = $negotiate->getMedia(array('application/json', 'application/xml'));
        $this->assertSame($expect, $actual);
        
        // media is available but quality level is not acceptable
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT' => 'application/json, application/xml, text/*, foo/bar;q=0',
        ));
        $expect = false;
        $actual = $negotiate->getMedia(array('foo/bar'));
        $this->assertSame($expect, $actual);
        
        // override with file extension
        $negotiate = $this->newNegotiate(array(
            'HTTP_ACCEPT' => 'text/html, text/xhtml, text/plain',
            'REQUEST_URI' => '/path/to/resource.json',
        ));
        $expect = 'application/json';
        $actual = $negotiate->getMedia(array('text/html', 'application/json'));
        $this->assertSame($expect, $actual);
    }
}
