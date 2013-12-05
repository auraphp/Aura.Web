<?php
namespace Aura\Web\Request;

class AcceptTest extends \PHPUnit_Framework_TestCase
{
    protected function newAccept($server = array())
    {
        $charset = new Accept\Charset($server);
        $encoding = new Accept\Encoding($server);
        $language = new Accept\Language($server);
        $media = new Accept\Media($server);
        
        return new Accept(
            $charset,
            $encoding,
            $language,
            $media,
            $server
        );
    }

    /**
     * @dataProvider charsetProvider
     * @param $accept
     * @param $expect
     * @param $setType
     * @param $valueType
     */
    public function testGetCharset($accept, $expect, $setType, $valueType)
    {
        $accept = $this->newAccept($accept);
        $actual = $accept->getCharset();
        
        $this->verifySet($actual, $expect, $setType, $valueType);
    }

    /**
     * @dataProvider encodingProvider
     * @param $accept
     * @param $expect
     * @param $setType
     * @param $valueType
     */
    public function testGetEncoding($accept, $expect, $setType, $valueType)
    {
        $accept = $this->newAccept($accept);
        $actual = $accept->getEncoding();
        
        $this->verifySet($actual, $expect, $setType, $valueType);
    }

    /**
     * @dataProvider languageProvider
     * @param $accept
     * @param $expect
     * @param $setType
     * @param $valueType
     */
    public function testGetLanguage($accept, $expect, $setType, $valueType)
    {
        $accept = $this->newAccept($accept);
        $actual = $accept->getLanguage();

        $this->verifySet($actual, $expect, $setType, $valueType);
    }

    /**
     * @dataProvider mediaProvider
     * @param $accept
     * @param $expect
     * @param $setType
     * @param $valueType
     */
    public function testGetMedia($accept, $expected, $setType, $valueType)
    {
        $accept = $this->newAccept($accept);
        $actual = $accept->getMedia();

        $this->verifySet($actual, $expected, $setType, $valueType);
    }

    /**
     * @dataProvider charsetNegotiateProvider
     * @param $accept
     * @param $available
     * @param $expected
     */
    public function testGetCharset_negotiate($accept, $available, $expected)
    {
        $accept = $this->newAccept($accept);

        $actual = $accept->getCharset($available);

        if ($expected === false) {
            $this->assertFalse($expected, $actual);
        } else {
            $this->assertInstanceOf('Aura\Web\Request\Accept\Value\Charset', $actual);
            $this->assertSame($expected, $actual->getValue());
        }
    }

    /**
     * @dataProvider encodingNegotiateProvider
     * @param $accept
     * @param $available
     * @param $expected
     */
    public function testGetEncoding_negotiate($accept, $available, $expected)
    {
        $accept = $this->newAccept($accept);

        $actual = $accept->getEncoding($available);

        if ($expected === false) {
            $this->assertFalse($actual);
        } else {
            $this->assertInstanceOf('Aura\Web\Request\Accept\Value\Encoding', $actual);
            $this->assertSame($expected, $actual->getValue());
        }
    }

    /**
     * @dataProvider languageNegotiateProvider
     * @param $accept
     * @param $available
     * @param $expected
     */
    public function testGetLanguage_negotiate($accept, $available, $expected)
    {
        $accept = $this->newAccept($accept);

        $actual = $accept->getLanguage($available);

        if ($expected === false) {
            $this->assertFalse($actual);
        } else {
            $this->assertInstanceOf('Aura\Web\Request\Accept\Value\Language', $actual);
            $this->assertSame($expected, $actual->getValue());
        }
    }

    /**
     * @dataProvider mediaNegotiateProvider
     * @param $accept
     * @param $available
     * @param $expected
     */
    public function testGetMedia_negotiate($accept, $available, $expected)
    {
        $accept = $this->newAccept($accept);

        $actual = $accept->getMedia($available);

        if ($expected === false) {
            $this->assertFalse($actual);
        } else {
            $this->assertInstanceOf('Aura\Web\Request\Accept\Value\Media', $actual);
            $this->assertSame($expected, $actual->getValue());
        }
    }

    protected function verifySet($set, $expected, $setType, $valueType)
    {
        $this->assertInstanceOf($setType, $set);
        $this->assertSameSize($set, $expected);

        foreach ($set as $key => $item) {
            $this->assertInstanceOf($valueType, $item);
            foreach ($expected[$key] as $func => $value) {
                if ($func != 'string') {
                    $func = 'get' . $func;
                } else {
                    $func = '__toString';
                }

                $this->assertEquals($value, $item->$func());
            }
        }
    }

    public function charsetProvider()
    {
        return array(
            array(
                'accept' => array(
                    'HTTP_ACCEPT_CHARSET' => 'iso-8859-5, unicode-1-1;q=0.8',
                ),
                'expected' => array(
                    array(
                        'value' => 'iso-8859-5',
                        'priority' => 1.0,
                    ),
                    array(
                        'value' => 'ISO-8859-1',
                        'priority' => 1.0,
                    ),
                    array(
                        'value' => 'unicode-1-1',
                        'priority' => 0.8,
                    ),
                ),
                'setType' => 'Aura\Web\Request\Accept\Charset',
                'valueType' => 'Aura\Web\Request\Accept\Value\Charset',
            )
        );
    }

    public function charsetNegotiateProvider()
    {
        return array(
            array(
                'accept' => array('HTTP_ACCEPT_CHARSET' => 'iso-8859-5, unicode-1-1, *'),
                'available' => array(),
                'expected' => false,
            ),
            array(
                'accept' => array('HTTP_ACCEPT_CHARSET' => 'iso-8859-5, unicode-1-1, *'),
                'available' => array('foo', 'bar'),
                'expected' => 'foo'
            ),
            array(
                'accept' => array('HTTP_ACCEPT_CHARSET' => 'iso-8859-5, unicode-1-1, *'),
                'available' => array('foo', 'UniCode-1-1'),
                'expected' => 'UniCode-1-1'
            ),
            array(
                'accept' => array(),
                'available' => array('ISO-8859-5', 'foo'),
                'expected' => 'ISO-8859-5'
            ),
            array(
                'accept' => array('HTTP_ACCEPT_CHARSET' => 'ISO-8859-1, baz;q=0'),
                'available' => array('baz'),
                'expected' => false
            ),
        );
    }

    public function encodingProvider()
    {
        return array(
            array(
                'accept' => array('HTTP_ACCEPT_ENCODING' => 'compress;q=0.5, gzip;q=1.0'),
                'expect' => array(
                    array('value' => 'gzip', 'priority' => 1.0),
                    array('value' => 'compress', 'priority' => 0.5)
                ),
                'setType' => 'Aura\Web\Request\Accept\Encoding',
                'valueType' => 'Aura\Web\Request\Accept\Value\Encoding',
            )
        );
    }

    public function encodingNegotiateProvider()
    {
        return array(
            array(
                'accept' => array('HTTP_ACCEPT_ENCODING' => 'gzip, compress, *',),
                'available' => array(),
                'expected' => false,
            ),
            array(
                'accept' => array('HTTP_ACCEPT_ENCODING' => 'gzip, compress, *'),
                'available' => array('foo', 'bar'),
                'expected' => 'foo',
            ),
            array(
                'accept' => array('HTTP_ACCEPT_ENCODING' => 'gzip, compress, *',),
                'available' => array('foo', 'GZIP'),
                'expected' => 'GZIP',
            ),
            array(
                'accept' => array('HTTP_ACCEPT_ENCODING' => 'gzip, compress, *',),
                'available' => array('gzip', 'compress'),
                'expected' => 'gzip',
            ),
            array(
                'accept' => array('HTTP_ACCEPT_ENCODING' => 'gzip, compress, foo;q=0'),
                'available' => array('foo'),
                'expected' => false,
            ),
        );
    }

    public function mediaNegotiateProvider()
    {
        return array(
            array(
                // nothing available
                'accept' => array('HTTP_ACCEPT' => 'application/json, application/xml, text/*, */*'),
                'available' => array(),
                'expected' => false,
            ),
            array(
                // explicitly accepts */*, and no matching media are available
                'accept' => array('HTTP_ACCEPT' => 'application/json, application/xml, text/*, */*'),
                'available' => array('foo/bar', 'baz/dib'),
                'expected' => 'foo/bar',
            ),
            array(
                // explictly accepts application/xml, which is explictly available.
                // note that it returns the *available* value, which is determined
                // by the developer, not the acceptable value, which is determined
                // by the user/client/headers.
                'accept' => array('HTTP_ACCEPT' => 'application/json, application/xml, text/*, */*'),
                'available' => array('application/XML', 'text/csv'),
                'expected' => 'application/XML',
            ),
            array(
                // a subtype is available
                'accept' => array('HTTP_ACCEPT' => 'application/json, application/xml, text/*, */*'),
                'available' => array('foo/bar', 'text/csv', 'baz/qux'),
                'expected' => 'text/csv',
            ),
            array(
                // no acceptable media specified, use first available
                'accept' => array(),
                'available' => array('application/json', 'application/xml'),
                'expected' => 'application/json',
            ),
            array(
                // media is available but quality level is not acceptable
                'accept' => array('HTTP_ACCEPT' => 'application/json, application/xml, text/*, foo/bar;q=0'),
                'available' => array('foo/bar'),
                'expected' => false,
            ),
            array(
                // override with file extension
                'accept' => array(
                    'HTTP_ACCEPT' => 'text/html, text/xhtml, text/plain',
                    'REQUEST_URI' => '/path/to/resource.json',
                ),
                'available' => array('text/html', 'application/json'),
                'expected' => 'application/json',
            )
        );
    }

    public function languageProvider()
    {
        return array(
            array(
                'accept' => array(),
                'expect' => array(),
                'setType' => 'Aura\Web\Request\Accept\Language',
                'valueType' => 'Aura\Web\Request\Accept\Value\Language',
            ),
            array(
                'accept' => array(
                    'HTTP_ACCEPT_LANGUAGE' => '*',
                ),
                'expect' => array(
                    array('type' => '*', 'subtype' => false, 'value' => '*',  'priority' => 1.0, 'parameters' => array())
                ),
                'setType' => 'Aura\Web\Request\Accept\Language',
                'valueType' => 'Aura\Web\Request\Accept\Value\Language',
            ),
            array(
                'accept' => array(
                    'HTTP_ACCEPT_LANGUAGE' => 'en-US, en-GB, en, *',
                ),
                'expect' => array(
                    array('type' => 'en', 'subtype' => 'US', 'value' => 'en-US', 'priority' => 1.0, 'parameters' => array()),
                    array('type' => 'en', 'subtype' => 'GB', 'value' => 'en-GB', 'priority' => 1.0, 'parameters' => array()),
                    array('type' => 'en', 'subtype' => false, 'value' => 'en', 'priority' => 1.0, 'parameters' => array()),
                    array('type' => '*', 'subtype' => false, 'value' => '*',  'priority' => 1.0, 'parameters' => array())
                ),
                'setType' => 'Aura\Web\Request\Accept\Language',
                'valueType' => 'Aura\Web\Request\Accept\Value\Language',
            ),
        );
    }

    public function mediaProvider()
    {
        return array(
            array(
                'accept' => array('HTTP_ACCEPT' => 'text/*;q=0.9, text/html, text/xhtml;q=0.8'),
                'expect' => array(
                    array(
                        'type' => 'text',
                        'subtype' => 'html',
                        'value' => 'text/html',
                        'priority' => 1.0,
                        'string' => 'text/html;q=1',
                        'parameters' => array(),
                    ),
                    array(
                        'type' => 'text',
                        'subtype' => '*',
                        'value' => 'text/*',
                        'priority' => 0.9,
                        'string' => 'text/*;q=0.9',
                        'parameters' => array(),
                    ),
                    array(
                        'type' => 'text',
                        'subtype' => 'xhtml',
                        'value' => 'text/xhtml',
                        'priority' => 0.8,
                        'string' => 'text/xhtml;q=0.8',
                        'parameters' => array(),
                    ),
                ),
                'setType' => 'Aura\Web\Request\Accept\Media',
                'valueType' => 'Aura\Web\Request\Accept\Value\Media',
            ),
            array(
                'accept' => array('HTTP_ACCEPT' => 'text/json;version=1,text/html;q=1;version=2,application/xml+xhtml;q=0'),
                'expect' => array(
                    array(
                        'type' => 'text',
                        'subtype' => 'json',
                        'value' => 'text/json',
                        'priority' => 1.0,
                        'string' => 'text/json;q=1;version=1',
                        'parameters' => array('version' => 1),
                    ),
                    array(
                        'type' => 'text',
                        'subtype' => 'html',
                        'value' => 'text/html',
                        'priority' => 1.0,
                        'string' => 'text/html;q=1;version=2',
                        'parameters' => array('version' => 2),
                    ),
                    array(
                        'type' => 'application',
                        'subtype' => 'xml+xhtml',
                        'value' => 'application/xml+xhtml',
                        'priority' => 0,
                        'string' => 'application/xml+xhtml;q=0',
                        'parameters' => array(),
                    ),
                ),
                'setType' => 'Aura\Web\Request\Accept\Media',
                'valueType' => 'Aura\Web\Request\Accept\Value\Media',
            ),
            array(
                'accept' => array('HTTP_ACCEPT' => 'text/json;version=1;foo=bar,text/html;version=2,application/xml+xhtml'),
                'expect' => array(
                    array(
                        'type' => 'text',
                        'subtype' => 'json',
                        'value' => 'text/json',
                        'priority' => 1.0,
                        'string' => 'text/json;q=1;version=1;foo=bar',
                        'parameters' => array('version' => 1, 'foo' => 'bar'),
                    ),
                    array(
                        'type' => 'text',
                        'subtype' => 'html',
                        'value' => 'text/html',
                        'priority' => 1.0,
                        'string' => 'text/html;q=1;version=2',
                        'parameters' => array('version' => 2),
                    ),
                    array(
                        'type' => 'application',
                        'subtype' => 'xml+xhtml',
                        'value' => 'application/xml+xhtml',
                        'priority' => 1.0,
                        'string' => 'application/xml+xhtml;q=1',
                        'parameters' => array(),
                    ),
                ),
                'setType' => 'Aura\Web\Request\Accept\Media',
                'valueType' => 'Aura\Web\Request\Accept\Value\Media',
            ),
            array(
                'accept' => array('HTTP_ACCEPT' => 'text/json;q=0.9;version=1;foo="bar"'),
                'expect' => array(
                    array(
                        'type' => 'text',
                        'subtype' => 'json',
                        'value' => 'text/json',
                        'priority' => 0.9,
                        'string' => 'text/json;q=0.9;version=1;foo=bar',
                        'parameters' => array('version' => 1, 'foo' => 'bar'),
                    ),
                ),
                'setType' => 'Aura\Web\Request\Accept\Media',
                'valueType' => 'Aura\Web\Request\Accept\Value\Media',
            ),
        );
    }

    public function languageNegotiateProvider()
    {
        return array(
            array(
                'accept' => array('HTTP_ACCEPT_LANGUAGE' => 'en-US, en-GB, en, *'),
                'available' => array(),
                'expected' => false,
            ),
            array(
                'accept' => array('HTTP_ACCEPT_LANGUAGE' => 'en-US, en-GB, en, *'),
                'available' => array('foo-bar' , 'baz-dib'),
                'expected' => 'foo-bar',
            ),
            array(
                'accept' => array('HTTP_ACCEPT_LANGUAGE' => 'en-US, en-GB, en, *'),
                'available' => array('en-gb', 'fr-FR'),
                'expected' => 'en-gb',
            ),
            array(
                'accept' => array('HTTP_ACCEPT_LANGUAGE' => 'en-US, en-GB, en, *'),
                'available' => array('foo-bar', 'en-zo', 'baz-qux'),
                'expected' => 'en-zo',
            ),
            array(
                'accept' => array(),
                'available' => array('en-us', 'en-gb'),
                'expected' => 'en-us',
            ),
            array(
                'accept' => array('HTTP_ACCEPT_LANGUAGE' => 'en-us, en-gb, en, foo-bar;q=0'),
                'available' => array('foo-bar'),
                'expected' => false
            )
        );
    }
}
