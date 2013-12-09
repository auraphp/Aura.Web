<?php
namespace Aura\Web\Request;

class AcceptTest extends \PHPUnit_Framework_TestCase
{
    protected function newAccept($server = array())
    {
        $value_factory = new Accept\Value\ValueFactory;
        $charset = new Accept\Charset($value_factory, $server);
        $encoding = new Accept\Encoding($value_factory, $server);
        $language = new Accept\Language($value_factory, $server);
        $media = new Accept\Media($value_factory, $server);
        
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
     * @param $values_class
     * @param $value_class
     */
    public function testGetCharset($accept, $expect, $values_class, $value_class)
    {
        $accept = $this->newAccept($accept);
        $actual = $accept->charset;
        $this->assertAcceptValues($actual, $expect, $values_class, $value_class);
    }

    /**
     * @dataProvider encodingProvider
     * @param $accept
     * @param $expect
     * @param $values_class
     * @param $value_class
     */
    public function testGetEncoding($accept, $expect, $values_class, $value_class)
    {
        $accept = $this->newAccept($accept);
        $actual = $accept->encoding;
        $this->assertAcceptValues($actual, $expect, $values_class, $value_class);
    }

    /**
     * @dataProvider languageProvider
     * @param $accept
     * @param $expect
     * @param $values_class
     * @param $value_class
     */
    public function testGetLanguage($accept, $expect, $values_class, $value_class)
    {
        $accept = $this->newAccept($accept);
        $actual = $accept->language;
        $this->assertAcceptValues($actual, $expect, $values_class, $value_class);
    }

    /**
     * @dataProvider mediaProvider
     * @param $accept
     * @param $expect
     * @param $values_class
     * @param $value_class
     */
    public function testGetMedia($accept, $expected, $values_class, $value_class)
    {
        $accept = $this->newAccept($accept);
        $actual = $accept->media;

        $this->assertAcceptValues($actual, $expected, $values_class, $value_class);
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

        $actual = $accept->charset->negotiate($available);

        if ($expected === false) {
            $this->assertFalse($expected, $actual);
        } else {
            $this->assertInstanceOf('Aura\Web\Request\Accept\Value\Charset', $actual->available);
            $this->assertSame($expected, $actual->available->getValue());
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

        $actual = $accept->encoding->negotiate($available);

        if ($expected === false) {
            $this->assertFalse($actual);
        } else {
            $this->assertInstanceOf('Aura\Web\Request\Accept\Value\Encoding', $actual->available);
            $this->assertSame($expected, $actual->available->getValue());
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

        $actual = $accept->language->negotiate($available);

        if ($expected === false) {
            $this->assertFalse($actual);
        } else {
            $this->assertInstanceOf('Aura\Web\Request\Accept\Value\Language', $actual->available);
            $this->assertSame($expected, $actual->available->getValue());
        }
    }

    /**
     * @dataProvider mediaNegotiateProvider
     * @param $accept
     * @param $available
     * @param $expected_value
     * @param $expected_params
     */
    public function testGetMedia_negotiate($accept, $available, $expected_value, $expected_params)
    {
        $accept = $this->newAccept($accept);

        $actual = $accept->media->negotiate($available);

        if ($expected_value === false) {
            $this->assertFalse($actual);
        } else {
            $this->assertInstanceOf('Aura\Web\Request\Accept\Value\Media', $actual->available);
            $this->assertSame($expected_value, $actual->available->getValue());
            $this->assertSame($expected_params, $actual->available->getParameters());
        }
    }

    protected function assertAcceptValues($actual, $expect, $values_class, $value_class)
    {
        $this->assertInstanceOf($values_class, $actual);
        $this->assertSameSize($actual->get(), $expect);

        foreach ($actual as $key => $item) {
            $this->assertInstanceOf($value_class, $item);
            foreach ($expect[$key] as $func => $value) {
                $func = 'get' . $func;
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
                        'quality' => 1.0,
                    ),
                    array(
                        'value' => 'ISO-8859-1',
                        'quality' => 1.0,
                    ),
                    array(
                        'value' => 'unicode-1-1',
                        'quality' => 0.8,
                    ),
                ),
                'values_class' => 'Aura\Web\Request\Accept\Charset',
                'value_class' => 'Aura\Web\Request\Accept\Value\Charset',
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
                    array('value' => 'gzip', 'quality' => 1.0),
                    array('value' => 'compress', 'quality' => 0.5)
                ),
                'values_class' => 'Aura\Web\Request\Accept\Encoding',
                'value_class' => 'Aura\Web\Request\Accept\Value\Encoding',
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
                'expected_value' => false,
                'expected_params' => array(),
            ),
            array(
                // explicitly accepts */*, and no matching media are available
                'accept' => array('HTTP_ACCEPT' => 'application/json, application/xml, text/*, */*'),
                'available' => array('foo/bar', 'baz/dib'),
                'expected_value' => 'foo/bar',
                'expected_params' => array(),
            ),
            array(
                // explictly accepts application/xml, which is explictly available.
                // note that it returns the *available* value, which is determined
                // by the developer, not the acceptable value, which is determined
                // by the user/client/headers.
                'accept' => array('HTTP_ACCEPT' => 'application/json, application/xml, text/*, */*'),
                'available' => array('application/XML', 'text/csv'),
                'expected_value' => 'application/XML',
                'expected_params' => array(),
            ),
            array(
                // a subtype is available
                'accept' => array('HTTP_ACCEPT' => 'application/json, application/xml, text/*, */*'),
                'available' => array('foo/bar', 'text/csv', 'baz/qux'),
                'expected_value' => 'text/csv',
                'expected_params' => array(),
            ),
            array(
                // no acceptable media specified, use first available
                'accept' => array(),
                'available' => array('application/json', 'application/xml'),
                'expected_value' => 'application/json',
                'expected_params' => array(),
            ),
            array(
                // media is available but quality level is not acceptable
                'accept' => array('HTTP_ACCEPT' => 'application/json, application/xml, text/*, foo/bar;q=0'),
                'available' => array('foo/bar'),
                'expected_value' => false,
                'expected_params' => array(),
            ),
            array(
                // override with file extension
                'accept' => array(
                    'HTTP_ACCEPT' => 'text/html, text/xhtml, text/plain',
                    'REQUEST_URI' => '/path/to/resource.json',
                ),
                'available' => array('text/html', 'application/json'),
                'expected_value' => 'application/json',
                'expected_params' => array(),
            ),
            array(
                // check against parameters when they are available
                'accept' => array('HTTP_ACCEPT' => 'text/html;level=2, text/html;level=1;q=0.5'),
                'available' => array('text/html;level=1'),
                'expected_value' => 'text/html',
                'expected_params' => array('level' => '1'),
            ),
            array(
                // check against parameters when they are not available
                'accept' => array('HTTP_ACCEPT' => 'text/html;level=2, text/html;level=1;q=0.5'),
                'available' => array('text/html;level=3'),
                'expected_value' => false,
                'expected_params' => array(),
            ),
        );
    }

    public function languageProvider()
    {
        return array(
            array(
                'accept' => array(),
                'expect' => array(),
                'values_class' => 'Aura\Web\Request\Accept\Language',
                'value_class' => 'Aura\Web\Request\Accept\Value\Language',
            ),
            array(
                'accept' => array(
                    'HTTP_ACCEPT_LANGUAGE' => '*',
                ),
                'expect' => array(
                    array('type' => '*', 'subtype' => false, 'value' => '*',  'quality' => 1.0, 'parameters' => array())
                ),
                'values_class' => 'Aura\Web\Request\Accept\Language',
                'value_class' => 'Aura\Web\Request\Accept\Value\Language',
            ),
            array(
                'accept' => array(
                    'HTTP_ACCEPT_LANGUAGE' => 'en-US, en-GB, en, *',
                ),
                'expect' => array(
                    array('type' => 'en', 'subtype' => 'US', 'value' => 'en-US', 'quality' => 1.0, 'parameters' => array()),
                    array('type' => 'en', 'subtype' => 'GB', 'value' => 'en-GB', 'quality' => 1.0, 'parameters' => array()),
                    array('type' => 'en', 'subtype' => false, 'value' => 'en', 'quality' => 1.0, 'parameters' => array()),
                    array('type' => '*', 'subtype' => false, 'value' => '*',  'quality' => 1.0, 'parameters' => array())
                ),
                'values_class' => 'Aura\Web\Request\Accept\Language',
                'value_class' => 'Aura\Web\Request\Accept\Value\Language',
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
                        'quality' => 1.0,
                        'parameters' => array(),
                    ),
                    array(
                        'type' => 'text',
                        'subtype' => '*',
                        'value' => 'text/*',
                        'quality' => 0.9,
                        'parameters' => array(),
                    ),
                    array(
                        'type' => 'text',
                        'subtype' => 'xhtml',
                        'value' => 'text/xhtml',
                        'quality' => 0.8,
                        'parameters' => array(),
                    ),
                ),
                'values_class' => 'Aura\Web\Request\Accept\Media',
                'value_class' => 'Aura\Web\Request\Accept\Value\Media',
            ),
            array(
                'accept' => array('HTTP_ACCEPT' => 'text/json;version=1,text/html;q=1;version=2,application/xml+xhtml;q=0'),
                'expect' => array(
                    array(
                        'type' => 'text',
                        'subtype' => 'json',
                        'value' => 'text/json',
                        'quality' => 1.0,
                        'parameters' => array('version' => 1),
                    ),
                    array(
                        'type' => 'text',
                        'subtype' => 'html',
                        'value' => 'text/html',
                        'quality' => 1.0,
                        'parameters' => array('version' => 2),
                    ),
                    array(
                        'type' => 'application',
                        'subtype' => 'xml+xhtml',
                        'value' => 'application/xml+xhtml',
                        'quality' => 0,
                        'parameters' => array(),
                    ),
                ),
                'values_class' => 'Aura\Web\Request\Accept\Media',
                'value_class' => 'Aura\Web\Request\Accept\Value\Media',
            ),
            array(
                'accept' => array('HTTP_ACCEPT' => 'text/json;version=1;foo=bar,text/html;version=2,application/xml+xhtml'),
                'expect' => array(
                    array(
                        'type' => 'text',
                        'subtype' => 'json',
                        'value' => 'text/json',
                        'quality' => 1.0,
                        'parameters' => array('version' => 1, 'foo' => 'bar'),
                    ),
                    array(
                        'type' => 'text',
                        'subtype' => 'html',
                        'value' => 'text/html',
                        'quality' => 1.0,
                        'parameters' => array('version' => 2),
                    ),
                    array(
                        'type' => 'application',
                        'subtype' => 'xml+xhtml',
                        'value' => 'application/xml+xhtml',
                        'quality' => 1.0,
                        'parameters' => array(),
                    ),
                ),
                'values_class' => 'Aura\Web\Request\Accept\Media',
                'value_class' => 'Aura\Web\Request\Accept\Value\Media',
            ),
            array(
                'accept' => array('HTTP_ACCEPT' => 'text/json;q=0.9;version=1;foo="bar"'),
                'expect' => array(
                    array(
                        'type' => 'text',
                        'subtype' => 'json',
                        'value' => 'text/json',
                        'quality' => 0.9,
                        'parameters' => array('version' => 1, 'foo' => 'bar'),
                    ),
                ),
                'values_class' => 'Aura\Web\Request\Accept\Media',
                'value_class' => 'Aura\Web\Request\Accept\Value\Media',
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
