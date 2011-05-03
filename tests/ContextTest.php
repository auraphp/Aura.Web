<?php

namespace aura\web;

require_once 'PhpStream.php';

class ContextTest extends \PHPUnit_Framework_TestCase
{
    protected function newContext($csrf = null, $agents = null)
    {
        return new Context($GLOBALS, $csrf, $agents);
    }
    
    protected function newCsrf()
    {
        return new Csrf('secret', 'usrid');
    }
    
    protected function reset()
    {
        $GLOBALS['_GET']    = array();
        $GLOBALS['_POST']   = array();
        $GLOBALS['_SERVER'] = array();
        $GLOBALS['_FILES']  = array();
        $GLOBALS['_ENV']    = array();
        $GLOBALS['_FILES']  = array();
    }

    public function testHttpMethodOverload()
    {
        $this->reset();
        $_POST['X-HTTP-Method-Override']        = 'header-takes-precedence';
        $_SERVER['REQUEST_METHOD']              = 'POST';
        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'PUT';
        $context    = $this->newContext();
        $actual = $context->getServer('REQUEST_METHOD');
        
        $this->assertSame('PUT', $actual);
        
        $this->reset();
        $_POST['X-HTTP-Method-Override']        = 'DELETE';
        $_SERVER['REQUEST_METHOD']              = 'POST';
        $context    = $this->newContext();
        $actual = $context->getServer('REQUEST_METHOD');
        
        $this->assertSame('DELETE', $actual);
    }
    
    public function test__get()
    {
        $this->reset();
        $context = $this->newContext();
        
        // test that we can access without causing an exception
        $context->get;
        $context->post;
        $context->server;
        $context->cookie;
        $context->env;
        $context->files;
        $context->header;
        
        // invalid or protected should cause an exception
        $this->setExpectedException('\UnexpectedValueException');
        $context->invalid;
    }

    public function testIsGet()
    {
        $this->reset();
        $context = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($context->isGet());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $context = $this->newContext();
        $this->assertTrue($context->isGet());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'NOT-GET';
        $context = $this->newContext();
        $this->assertFalse($context->isGet());
    }

    public function testIsPost()
    {
        $this->reset();
        $context = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($context->isPost());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $context = $this->newContext();
        $this->assertTrue($context->isPost());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'NOT-POST';
        $context = $this->newContext();
        $this->assertFalse($context->isPost());
    }

    public function testIsPut()
    {
        $this->reset();
        $context = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($context->isPut());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $context = $this->newContext();
        $this->assertTrue($context->isPut());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'NOT-PUT';
        $context = $this->newContext();
        $this->assertFalse($context->isPut());
    }

    public function testIsDelete()
    {
        $this->reset();
        $context = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($context->isDelete());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $context = $this->newContext();
        $this->assertTrue($context->isDelete());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'NOT-DELETE';
        $context = $this->newContext();
        $this->assertFalse($context->isDelete());
    }

    public function testIsHead()
    {
        $this->reset();
        $context = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($context->isHead());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $context = $this->newContext();
        $this->assertTrue($context->isHead());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'NOT-HEAD';
        $context = $this->newContext();
        $this->assertFalse($context->isHead());
    }

    public function testIsOptions()
    {
        $this->reset();
        $context = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($context->isOptions());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        $context = $this->newContext();
        $this->assertTrue($context->isOptions());
        
        $this->reset();
        $_SERVER['REQUEST_METHOD'] = 'NOT-OPTIONS';
        $context = $this->newContext();
        $this->assertFalse($context->isOptions());
    }

    public function testIsXhr()
    {
        $this->reset();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $context = $this->newContext();
        $this->assertTrue($context->isXhr());
        
        $this->reset();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XXX';
        $context = $this->newContext();
        $this->assertFalse($context->isXhr());
        
        $this->reset();
        $context = $this->newContext();
        
        // HTTP_X_REQUESTED_WITH not set
        $this->assertFalse($context->isXhr());
    }

    public function testIsCsrf()
    {
        $this->reset();
        $csrf  = $this->newCsrf();
        $_POST['__csrf_token'] = $csrf->generateToken();
        $context   = $this->newContext($this->newCsrf());
        
        $this->assertFalse($context->isCsrf());
        $this->assertTrue($context->isCsrf('invalid_key'));
        
        // if Csrf library is not provided an exception is thrown
        $this->reset();
        $context = $this->newContext();
        
        $this->setExpectedException('aura\web\Exception_Context');
        $context->isCsrf();
    }

    public function testIsSsl()
    {
        $this->reset();
        $context = $this->newContext();
        
        // HTTPS & SERVER_PORT not set
        $this->assertFalse($context->isSsl());
        
        $this->reset();
        $_SERVER['HTTPS'] = 'on';
        $context = $this->newContext();
        $this->assertTrue($context->isSsl());
        
        $this->reset();
        $_SERVER['SERVER_PORT'] = '443';
        $context = $this->newContext();
        $this->assertTrue($context->isSsl());
    }
    
    public function testIsMobile()
    {
        $agents = array(
            array('Android', 'Mozilla/5.0 (Linux; U; Android 2.1; en-us; Nexus One Build/ERD62) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 –Nexus'),
            array('BlackBerry', 'BlackBerry8330/4.3.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/105'),
            array('iPhone', 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16'),
            array('iPad', 'Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; es-es) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7B405'),
            array('Blazer', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; PalmSource/Palm-D062; Blazer/4.5) 16;320x320'),
            array('Brew', 'Mozilla/5.0 (compatible; Teleca Q7; Brew 3.1.5; U; en) 240X400 LGE VX9700'),
            array('IEMobile', 'LG-CT810/V10x IEMobile/7.11 Profile/MIDP-2.0 Configuration/CLDC-1.1 Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 7.11)'),
            array('iPod', 'Mozilla/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/3A100a Safari/419.3 '),
            array('KDDI', 'KDDI-KC31 UP.Browser/6.2.0.5 (GUI) MMP/2.0'),
            array('Kindle', 'Mozilla/4.0 (compatible; Linux 2.6.22) NetFront/3.4 Kindle/2.0 (screen 600x800)'),
            array('Maemo', 'Mozilla/4.0 (compatible; MSIE 6.0; ; Linux armv5tejl; U) Opera 8.02 [en_US] Maemo browser 0.4.31 N770/SU-18'),
            array('MOT-' ,'MOT-L6/0A.52.45R MIB/2.2.1 Profile/MIDP-2.0 Configuration/CLDC-1.1'),
            array('Nokia', 'Mozilla/4.0 (compatible; MSIE 5.0; Series80/2.0 Nokia9300/05.22 Profile/MIDP-2.0 Configuration/CLDC-1.1)'),
            array('SymbianOS', 'Mozilla/5.0 (SymbianOS/9.1; U; en-us) AppleWebKit/413 (KHTML, like Gecko) Safari/413 es61i'),
            array('UP.Browser', 'OPWV-SDK UP.Browser/7.0.2.3.119 (GUI) MMP/2.0 Push/PO'),
            array('UP.Link', 'HTC-ST7377/1.59.502.3 (67150) Opera/9.50 (Windows NT 5.1; U; en) UP.Link/6.3.1.17.0'),
            array('Opera Mobi', 'Opera/9.80 (S60; SymbOS; Opera Mobi/499; U; en-GB) Presto/2.4.18 Version/10.00'),
            array('Opera Mini', 'Opera/9.60 (J2ME/MIDP; Opera Mini/4.2.13918/488; U; en) Presto/2.2.0'),
            array('webOS', 'Mozilla/5.0 (webOS/1.0; U; en-US) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/1.0 Safari/525.27.1 Pre/1.0'),
            array('Playstation', 'Mozilla/5.0 (PLAYSTATION 3; 1.00)'),
            array('Windows CE', 'Mozilla/4.0 (compatible; MSIE 4.01; Windows CE; Sprint:PPC-6700; PPC; 240x320)'),
            array('Polaris', 'LG-LX600 Polaris/6.0 MMP/2.0 Profile/MIDP-2.1 Configuration/CLDC-1.1'),
            array('SEMC', 'SonyEricssonK608i/R2L/SN356841000828910 Browser/SEMC-Browser/4.2 Profile/MIDP-2.0 Configuration/CLDC-1.1'),
            array('NetFront', 'Mozilla/4.0 (compatible;MSIE 6.0;Windows95;PalmSource) Netfront/3.0;8;320x320'),
            array('Fennec', 'Mozilla/5.0 (X11; U; Linux armv61; en-US; rv:1.9.1b2pre) Gecko/20081015 Fennec/1.0a1'),
        );
        
        // test each of the known agents
        foreach ($agents as $agent) {
            $this->reset();
            $pattern = $agent[0];
            $_SERVER['HTTP_USER_AGENT'] = $agent[1];
            $context = $this->newContext();
            $this->assertSame($pattern, $context->isMobile());
        }
        
        // test an added agent
        $this->reset();
        $_SERVER['HTTP_USER_AGENT'] = 'Foo/1.1';
        $agents = array('mobile' => array('Foo'));
        $context = $this->newContext(null, $agents);
        $this->assertSame('Foo', $context->isMobile());
        
        // test an unknown agent
        $_SERVER['HTTP_USER_AGENT'] = 'NoSuchAgent/1.0';
        $context = $this->newContext();
        $this->assertFalse($context->isMobile());
        
        // try to get it again, for code coverage
        $this->assertFalse($context->isMobile());
        
    }
    
    public function testIsCrawler()
    {
        $agents = array(
            array('Google', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'),
            array('Google', 'Mozilla/5.0 (compatible) Feedfetcher-Google; (+http://www.google.com/feedfetcher.html)'),
            array('Ask', 'Mozilla/5.0 (compatible; Ask Jeeves/Teoma; +http://about.ask.com/en/docs/about/webmasters.shtml)'),
            array('Baidu', 'Baiduspider+(+http://www.baidu.com/search/spider.htm)'),
            array('Yahoo', 'Mozilla/5.0 (compatible; Yahoo! Slurp/3.0; http://help.yahoo.com/help/us/ysearch/slurp)'),
            array('Nutch', 'GeoHasher/Nutch-1.0 (GeoHasher Web Search Engine; geohasher.gotdns.org;'),
            array('Y!J', 'Y!J-BRI/0.0.1 crawler ( http://help.yahoo.co.jp/help/jp/search/indexing/indexing-15.html)'),
            array('Danger hiptop', 'Mozilla/5.0 (Danger hiptop 3.3; U; AvantGo 3.2)'),
            array('MSR-ISRCCrawler', 'MSR-ISRCCrawler'),
            array('Y!OASIS', 'Y!OASIS/TEST no-ad Mozilla/4.08 [en] (X11; I; FreeBSD 2.2.8-STABLE i386)'),
            array('gsa-crawler', 'gsa-crawler (Enterprise; GID-01422; me@company.com)'),
            array('librabot' ,'librabot/1.0 (+http://search.msn.com/msnbot.htm)'),
            array('llssbot', 'llssbot/1.0(+http://labs.live.com;llssbot@microsoft.com)'),
            array('bingbot', 'Mozilla/5.0 (compatible; bingbot/2.0 +http://www.bing.com/bingbot.htm)'),
            array('MSMOBOT', 'msmobot/1.1 (+http://search.msn.com/msnbot.htm)'),
            array('MSNBot', 'msnbot-207-46-194-100.search.msn.com'),
            array('MSRBOT', 'MSRBOT (http://research.microsoft.com/research/sv/msrbot/)'),
            array('slurp', 'Slurp/2.0-condor_hourly (slurp@inktomi.com; http://www.inktomi.com/slurp.html)'),
            array('Scooter', 'Scooter/2.0 G.R.A.B. X2.0'),
            array('Yandex', 'Yandex/1.01.001 (compatible; Win16; I)'),
            array('Fast', 'FAST-WebCrawler/3.8 (atw-crawler at fast dot no; http://fast.no/support/crawler.asp)'),
            array('heritrix', 'Mozilla/5.0 (compatible; heritrix/1.12.1 +http://www.page-store.com) [email:paul@page-store.com]'),
            array('ia_archiver', 'ia_archiver/8.8 (Windows XP 7.2; en-US;)'),
            array('InternetArchive', 'internetarchive/0.8-dev (Nutch; http://lucene.apache.org/nutch/bot.html; nutch-agent@lucene.apache.org)'),
            array('archive.org_bot', 'Mozilla/5.0 (compatible; archive.org_bot/1.13.1x +http://crawler.archive.org)'),
            array('WordPress', 'wordpress/2.1.3'),
            array('Mp3Bot', 'Mozilla/5.0 (compatible; Mp3Bot/0.4; +http://mp3realm.org/mp3bot/)'),
            array('mp3Spider', 'mp3spider cn-search-devel'),
            array('Wget', 'Wget/1.12 (linux-gnu)')
        );
        
        foreach ($agents as $agent) {
            $this->reset();
            $pattern = $agent[0];
            $_SERVER['HTTP_USER_AGENT'] = $agent[1];
            $context = $this->newContext();
            $this->assertSame($pattern, $context->isCrawler());
        }
        
        // test an added agent
        $this->reset();
        $_SERVER['HTTP_USER_AGENT'] = 'Foo/1.1';
        $agents = array('crawler' => array('Foo'));
        $context = $this->newContext(null, $agents);
        $this->assertSame('Foo', $context->isCrawler());
        
        // test an unknown agent
        $this->reset();
        $_SERVER['HTTP_USER_AGENT'] = 'NoSuchAgent/1.0';
        $context = $this->newContext();
        $this->assertFalse($context->isCrawler());
        
        // try to get it again, for code coverage
        $this->assertFalse($context->isCrawler());
    }

    public function testGetQuery()
    {
        $this->reset();
        $_GET['foo'] = 'bar';
        $context = $this->newContext();
        
        $actual = $context->getQuery('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $context->getQuery('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $context->getQuery('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $context->getQuery();
        $this->assertSame(array('foo' => 'bar'), $actual);
    }
    
    public function testGetRawRequestBody()
    {
        $GLOBALS['aura\web\PhpStream'] = 'Hello World';
        stream_wrapper_unregister('php');
        stream_wrapper_register('php', 'aura\web\PhpStream');
        
        $this->reset();
        $_SERVER['CONTENT_TYPE']  = 'multipart/form-data';
        $context = $this->newContext();
        
        // if 'multipart/form-data' return null
        $actual = $context->getInput();
        $this->assertNull($actual);
        
        $this->reset();
        $_SERVER['CONTENT_TYPE'] = 'text/text';
        $context = $this->newContext();
        
        $actual = $context->getInput();
        $this->assertSame('Hello World', $actual);
        
        stream_wrapper_restore('php');
    }

    public function testPost()
    {
        $this->reset();
        $_POST['foo'] = 'bar';
        $context = $this->newContext();
        
        $actual = $context->getInput('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $context->getInput('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $context->getInput('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $context->getInput();
        $this->assertSame(array('foo' => 'bar'), $actual);
    }

    public function testGetCookie()
    {
        $this->reset();
        $_COOKIE['foo'] = 'bar';
        $context = $this->newContext();
        
        $actual = $context->getCookie('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $context->getCookie('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $context->getCookie('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $context->getCookie();
        $this->assertSame(array('foo' => 'bar'), $actual);
    }

    public function testGetEnv()
    {
        $this->reset();
        $_ENV['foo'] = 'bar';
        $context = $this->newContext();
        
        $actual = $context->getEnv('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $context->getEnv('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $context->getEnv('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $context->getEnv();
        $this->assertSame(array('foo' => 'bar'), $actual);
    }

    public function testGetServer()
    {
        $this->reset();
        $_SERVER['foo'] = 'bar';
        $context = $this->newContext();
        
        $actual = $context->getServer('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $context->getServer('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $context->getServer('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $context->getServer();
        $this->assertSame(array('foo' => 'bar'), $actual);
    }

    public function testFiles()
    {
        $this->reset();
        // single file
        $_FILES['foo'] = array(
            'error'     => null,
            'name'      => 'bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        // bar[]
        $_FILES['bar'] = array(
            'error'     => array(null, null),
            'name'      => array('foo', 'fooz'),
            'size'      => array(null, null),
            'tmp_name'  => array(null, null),
            'type'      => array(null, null),
        );
        // upload[file1]
        $_FILES['upload']['file1'] = array(
            'error'     => null,
            'name'      => 'file1.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $_FILES['upload']['file2'] = array(
            'error'     => null,
            'name'      => 'file2.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        
        $context = $this->newContext();
        
        $actual = $context->getInput('foo');
        $this->assertSame('bar', $actual['name']);
        
        $actual = $context->getInput('bar');
        $this->assertSame('foo',  $actual[0]['name']);
        $this->assertSame('fooz', $actual[1]['name']);
        
        $actual = $context->getInput('upload');
        $this->assertSame('file1.bar', $actual['file1']['name']);
        $this->assertSame('file2.bar', $actual['file2']['name']);
        
        $actual = $context->getInput('baz');
        $this->assertNull($actual);
        
        // return default
        $actual = $context->getInput('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $this->reset();
        $_FILES['foo'] = array(
            'error'     => null,
            'name'      => 'bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        
        $context    = $this->newContext();
        $actual = $context->getInput();
        $this->assertSame($_FILES, $actual);
    }

    public function testGetInput()
    {
        $this->reset();
        $_POST['foo']  = 'bar';
        $_FILES['baz'] = array(
            'error'     => null,
            'name'      => 'dib',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $context = $this->newContext();
        
        // match in post, not in files
        $actual = $context->getInput('foo');
        $this->assertSame('bar', $actual);
        
        // match in files, not in post
        $actual = $context->getInput('baz');
        $this->assertSame('dib', $actual['name']);
        
        // no matches returns null
        $actual = $context->getInput('zim');
        $this->assertNull($actual);
        
        // no matches returns alt
        $actual = $context->getInput('zim', 'gir');
        $this->assertSame('gir', $actual);
    }

    public function testgetInputWithPostAndFile()
    {
        $this->reset();
        $_FILES['baz'] = array(
            'error'     => null,
            'name'      => 'dib',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $_POST['baz']  = 'foo';
        $context                = $this->newContext();
        $actual             = $context->getInput('baz');
        
        $this->assertSame('dib', $actual['name']);
        $this->assertSame('foo', $actual[0]);
    }

    public function testgetInputWithMultiplePostsAndFile()
    {
        $this->reset();
        $_FILES['baz'] = array(
            'error'     => null,
            'name'      => 'dib',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $_POST['baz']  = array(
            'foo', 
            'name' => 'files-take-precedence',
            'var'  => 123,
            );
        $context                = $this->newContext();
        $actual             = $context->getInput('baz');
        
        $this->assertSame('dib', $actual['name']);
        $this->assertSame(123,   $actual['var']);
        $this->assertSame('foo', $actual[0]);
    }

    public function testgetInputWithPostAndMultipleFiles()
    {
        $this->reset();
        // baz[]
        $_POST['baz']  = 'bars';
        $_FILES['baz'] = array(
            'error'     => array(null, null),
            'name'      => array('foo', 'fooz'),
            'size'      => array(null, null),
            'tmp_name'  => array(null, null),
            'type'      => array(null, null),
        );
        // upload[file1]
        $_POST['upload']  = 'bars';
        $_FILES['upload']['file1'] = array(
            'error'     => null,
            'name'      => 'file1.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $_FILES['upload']['file2'] = array(
            'error'     => null,
            'name'      => 'file2.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $context    = $this->newContext();
        $actual = $context->getInput('baz');
        
        $this->assertSame('foo',  $actual[0]['name']);
        $this->assertSame('fooz', $actual[1]['name']);
        
        // post value is inserted into each file
        $this->assertSame('bars', $actual[0][0]);
        $this->assertSame('bars', $actual[1][0]);
        
        $actual = $context->getInput('upload');
        
        $this->assertSame('file1.bar', $actual['file1']['name']);
        $this->assertSame('file2.bar', $actual['file2']['name']);
        
        // post value is inserted into each file
        $this->assertSame('bars', $actual['file1'][0]);
        $this->assertSame('bars', $actual['file2'][0]);
    }

    public function testgetInputWithMultiplePostsAndMultipleFiles()
    {
        $this->reset();
        // baz[]
        $_POST['baz']  = array(
            'mars', 
            array(
                0      => 'bars',
                'name' => 'files-take-precedence',
        ));
        $_FILES['baz'] = array(
            'error'     => array(null, null),
            'name'      => array('foo', 'fooz'),
            'size'      => array(null, null),
            'tmp_name'  => array(null, null),
            'type'      => array(null, null),
        );
        
        // upload[file1]
        $_POST['upload']  = array(
            'file1' => 'mars', 
            'file2' => array(
                0      => 'bars',
                'name' => 'files-take-precedence'
        ));
        $_FILES['upload']['file1'] = array(
            'error'     => null,
            'name'      => 'file1.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $_FILES['upload']['file2'] = array(
            'error'     => null,
            'name'      => 'file2.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        
        $context    = $this->newContext();
        $actual = $context->getInput('baz');
        
        $this->assertSame('fooz', $actual[1]['name']);
        
        // post values is inserted
        $this->assertSame('mars', $actual[0][0]);
        $this->assertSame('bars', $actual[1][0]);
        
        $actual = $context->getInput('upload');
        
        $this->assertSame('file2.bar', $actual['file2']['name']);
        
        // post value is inserted
        $this->assertSame('mars', $actual['file1'][0]);
        $this->assertSame('bars', $actual['file2'][0]);
    }
    
    public function testGetHeader()
    {
        $this->reset();
        $_SERVER['HTTP_FOO'] = 'bar';
        $context = $this->newContext();
        
        $actual = $context->getHeader('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $context->getHeader('baz');
        $this->assertNull($actual);
        
        $actual = $context->getHeader('baz', 'dib');
        $this->assertSame('dib', $actual);
    }
    
    public function testGetAccept()
    {
        $this->reset();
        $_SERVER['HTTP_ACCEPT'] = 'text/*;q=0.9, text/html ,text/xhtml;q=0.8';
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US';
        
        $context    = $this->newContext();
        $expect = array(
            'text/html'  => 1.0,
            'text/*'     => 0.9,
            'text/xhtml' => 0.8,
        );
        $actual = $context->getAccept('type');
        $this->assertSame($expect, $actual);
        
        $actual = $context->getAccept('language');
        $expect = array('en-US' => 1.0);
        
        $this->assertSame($expect, $actual);
        
        $actual = $context->getAccept('charset', 'alt');
        $this->assertSame('alt', $actual);
        
        $expect = array(
            'type' => array(
                'text/html'  => 1.0,
                'text/*'     => 0.9,
                'text/xhtml' => 0.8,
            ),
            'language' => array(
                'en-US' => 1.0,
            ),
        );
        $actual = $context->getAccept();
        $this->assertSame($expect, $actual);
    }
    
    public function testXJsonIsRemoved()
    {
        $this->reset();
        $_SERVER['HTTP_X_JSON'] = 'remove-me';
        $context = $this->newContext();
        
        $actual = $context->getHeader('x-json');
        $this->assertNull($actual);
        
        $actual = $context->getServer('HTTP_X_JSON');
        $this->assertNull($actual);
    }
    
    public function testConstructorAgents()
    {
        $agents = array(
            'mobile' => array('foo'),
            'crawler' => array('bar'),
        );
        
        $context = $this->newContext(null, $agents);
        
        
    }
}
