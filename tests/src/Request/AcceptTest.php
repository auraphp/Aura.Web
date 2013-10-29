<?php
namespace Aura\Web\Request;

class AcceptTest extends \PHPUnit_Framework_TestCase
{
    protected function newAccept($server = array())
    {
        return new Accept($server);
    }
    
    public function testGetAccept()
    {
        $accept = $this->newAccept(array(
            'HTTP_ACCEPT' => 'text/*;q=0.9, text/html, text/xhtml;q=0.8',
        ));
        
        $expect = array(
            'text/html'  => 1.0,
            'text/*'     => 0.9,
            'text/xhtml' => 0.8,
        );
        
        $actual = $accept->getAccept();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testGetAcceptCharset()
    {
        $accept = $this->newAccept(array(
            'HTTP_ACCEPT_CHARSET' => 'iso-8859-5, unicode-1-1;q=0.8',
        ));
        
        $expect = array(
            'ISO-8859-1'  => 1.0,
            'iso-8859-5'  => 1.0,
            'unicode-1-1' => 0.8,
        );
        
        $actual = $accept->getAcceptCharset();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testGetAcceptEncoding()
    {
        $accept = $this->newAccept(array(
            'HTTP_ACCEPT_ENCODING' => 'compress;q=0.5, gzip;q=1.0',
        ));
        
        $expect = array(
            'gzip'     => 1.0,
            'compress' => 0.5,
        );
        
        $actual = $accept->getAcceptEncoding();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testGetAcceptLanguage()
    {
        $accept = $this->newAccept(array(
            'HTTP_ACCEPT_LANGUAGE' => 'en-US, en-GB, en, *',
        ));
        
        $expect = array(
            'en-US' => 1.0,
            'en-GB' => 1.0,
            'en' => 1.0,
            '*' => 1.0
        );
        
        $actual = $accept->getAcceptLanguage();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testGetCharset()
    {
        $accept = $this->newAccept(array(
            'HTTP_ACCEPT_CHARSET' => 'iso-8859-5, unicode-1-1, *',
        ));
        
        // nothing available
        $expect = false;
        $actual = $accept->getCharset(array());
        $this->assertSame($expect, $actual);
        
        // explicitly accepts *, and no matching charset available
        $expect = 'foo';
        $actual = $accept->getCharset(array('foo', 'bar'));
        $this->assertSame($expect, $actual);
        
        // explictly accepts unicode-1-1, which is explictly available.
        // note that it returns the *available* value, which is determined
        // by the developer, not the acceptable value, which is determined
        // by the user/client/headers.
        $expect = 'UniCode-1-1';
        $actual = $accept->getCharset(array('foo', 'UniCode-1-1'));
        $this->assertSame($expect, $actual);
        
        // no acceptable charset specified, use first available
        $accept = $this->newAccept();
        $expect = 'ISO-8859-5';
        $actual = $accept->getCharset(array('ISO-8859-5', 'foo'));
        $this->assertSame($expect, $actual);
        
        // charset is available but quality level is not acceptable
        $accept = $this->newAccept(array(
            'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1, baz;q=0',
        ));
        $expect = false;
        $actual = $accept->getCharset(array('baz'));
        $this->assertSame($expect, $actual);
    }
    
    public function testGetEncoding()
    {
        $accept = $this->newAccept(array(
            'HTTP_ACCEPT_ENCODING' => 'gzip, compress, *',
        ));
        
        // nothing available
        $expect = false;
        $actual = $accept->getEncoding(array());
        $this->assertSame($expect, $actual);
        
        // explicitly accepts *, and no matching encoding available
        $expect = 'foo';
        $actual = $accept->getEncoding(array('foo', 'bar'));
        $this->assertSame($expect, $actual);
        
        // explictly accepts compress, which is explictly available.
        // note that it returns the *available* value, which is determined
        // by the developer, not the acceptable value, which is determined
        // by the user/client/headers.
        $expect = 'GZIP';
        $actual = $accept->getEncoding(array('foo', 'GZIP'));
        $this->assertSame($expect, $actual);
        
        // no acceptable encoding specified, use first available
        $accept = $this->newAccept();
        $expect = 'gzip';
        $actual = $accept->getEncoding(array('gzip', 'compress'));
        $this->assertSame($expect, $actual);
        
        // encoding is available but quality level is not acceptable
        $accept = $this->newAccept(array(
            'HTTP_ACCEPT_ENCODING' => 'gzip, compress, foo;q=0',
        ));
        $expect = false;
        $actual = $accept->getEncoding(array('foo'));
        $this->assertSame($expect, $actual);
    }
    
    public function testGetLanguage()
    {
        $accept = $this->newAccept(array(
            'HTTP_ACCEPT_LANGUAGE' => 'en-US, en-GB, en, *',
        ));
        
        // nothing available
        $expect = false;
        $actual = $accept->getLanguage(array());
        $this->assertSame($expect, $actual);
        
        // explicitly accepts *, and no matching language available
        $expect = 'foo-bar';
        $actual = $accept->getLanguage(array('foo-bar', 'baz-dib'));
        $this->assertSame($expect, $actual);
        
        // explictly accepts en-gb, which is explictly available.
        // note that it returns the *available* value, which is determined
        // by the developer, not the acceptable value, which is determined
        // by the user/client/headers.
        $expect = 'en-gb';
        $actual = $accept->getLanguage(array('en-gb', 'fr-FR'));
        $this->assertSame($expect, $actual);
        
        // a subtype is available
        $expect = 'en-zo';
        $actual = $accept->getLanguage(array('foo-bar', 'en-zo', 'baz-qux'));
        $this->assertSame($expect, $actual);
        
        // no acceptable language specified, use first available
        $accept = $this->newAccept();
        $expect = 'en-us';
        $actual = $accept->getLanguage(array('en-us', 'en-gb'));
        $this->assertSame($expect, $actual);
        
        // language is available but quality level is not acceptable
        $accept = $this->newAccept(array(
            'HTTP_ACCEPT_LANGUAGE' => 'en-us, en-gb, en, foo-bar;q=0',
        ));
        $expect = false;
        $actual = $accept->getLanguage(array('foo-bar'));
        $this->assertSame($expect, $actual);
    }
    
    public function testGetMedia()
    {
        $accept = $this->newAccept(array(
            'HTTP_ACCEPT' => 'application/json, application/xml, text/*, */*',
        ));
        
        // nothing available
        $expect = false;
        $actual = $accept->getMedia(array());
        $this->assertSame($expect, $actual);
        
        // explicitly accepts */*, and no matching media are available
        $expect = 'foo/bar';
        $actual = $accept->getMedia(array('foo/bar', 'baz/dib'));
        $this->assertSame($expect, $actual);
        
        // explictly accepts application/xml, which is explictly available.
        // note that it returns the *available* value, which is determined
        // by the developer, not the acceptable value, which is determined
        // by the user/client/headers.
        $expect = 'application/XML';
        $actual = $accept->getMedia(array('application/XML', 'text/csv'));
        $this->assertSame($expect, $actual);
        
        // a subtype is available
        $expect = 'text/csv';
        $actual = $accept->getMedia(array('foo/bar', 'text/csv', 'baz/qux'));
        $this->assertSame($expect, $actual);
        
        // no acceptable media specified, use first available
        $accept = $this->newAccept();
        $expect = 'application/json';
        $actual = $accept->getMedia(array('application/json', 'application/xml'));
        $this->assertSame($expect, $actual);
        
        // media is available but quality level is not acceptable
        $accept = $this->newAccept(array(
            'HTTP_ACCEPT' => 'application/json, application/xml, text/*, foo/bar;q=0',
        ));
        $expect = false;
        $actual = $accept->getMedia(array('foo/bar'));
        $this->assertSame($expect, $actual);
        
        // override with file extension
        $accept = $this->newAccept(array(
            'HTTP_ACCEPT' => 'text/html, text/xhtml, text/plain',
            'REQUEST_URI' => '/path/to/resource.json',
        ));
        $expect = 'application/json';
        $actual = $accept->getMedia(array('text/html', 'application/json'));
        $this->assertSame($expect, $actual);
    }
}
