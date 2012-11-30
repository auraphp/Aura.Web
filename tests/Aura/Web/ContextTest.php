<?php
namespace Aura\Web;

require_once 'PhpStream.php';

class ContextTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        stream_wrapper_unregister('php');
        stream_wrapper_register('php', 'Aura\Web\PhpStream');
    }
    
    public function tearDown()
    {
        stream_wrapper_restore('php');
    }
    
    protected function newContext(array $agents = [])
    {
        return new Context($GLOBALS, $agents);
    }
    
    protected function reset()
    {
        $GLOBALS['_GET']    = [];
        $GLOBALS['_POST']   = [];
        $GLOBALS['_SERVER'] = [];
        $GLOBALS['_FILES']  = [];
        $GLOBALS['_ENV']    = [];
        $GLOBALS['_FILES']  = [];
        PhpStream::$content = '';
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
        $agents = [
            ['Android', 'Mozilla/5.0 (Linux; U; Android 2.1; en-us; Nexus One Build/ERD62) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 –Nexus'],
            ['BlackBerry', 'BlackBerry8330/4.3.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/105'],
            ['iPhone', 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16'],
            ['iPad', 'Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; es-es) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7B405'],
            ['Blazer', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; PalmSource/Palm-D062; Blazer/4.5) 16;320x320'],
            ['Brew', 'Mozilla/5.0 (compatible; Teleca Q7; Brew 3.1.5; U; en) 240X400 LGE VX9700'],
            ['IEMobile', 'LG-CT810/V10x IEMobile/7.11 Profile/MIDP-2.0 Configuration/CLDC-1.1 Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 7.11)'],
            ['iPod', 'Mozilla/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/3A100a Safari/419.3 '],
            ['KDDI', 'KDDI-KC31 UP.Browser/6.2.0.5 (GUI) MMP/2.0'],
            ['Kindle', 'Mozilla/4.0 (compatible; Linux 2.6.22) NetFront/3.4 Kindle/2.0 (screen 600x800)'],
            ['Maemo', 'Mozilla/4.0 (compatible; MSIE 6.0; ; Linux armv5tejl; U) Opera 8.02 [en_US] Maemo browser 0.4.31 N770/SU-18'],
            ['MOT-' ,'MOT-L6/0A.52.45R MIB/2.2.1 Profile/MIDP-2.0 Configuration/CLDC-1.1'],
            ['Nokia', 'Mozilla/4.0 (compatible; MSIE 5.0; Series80/2.0 Nokia9300/05.22 Profile/MIDP-2.0 Configuration/CLDC-1.1)'],
            ['SymbianOS', 'Mozilla/5.0 (SymbianOS/9.1; U; en-us) AppleWebKit/413 (KHTML, like Gecko) Safari/413 es61i'],
            ['UP.Browser', 'OPWV-SDK UP.Browser/7.0.2.3.119 (GUI) MMP/2.0 Push/PO'],
            ['UP.Link', 'HTC-ST7377/1.59.502.3 (67150) Opera/9.50 (Windows NT 5.1; U; en) UP.Link/6.3.1.17.0'],
            ['Opera Mobi', 'Opera/9.80 (S60; SymbOS; Opera Mobi/499; U; en-GB) Presto/2.4.18 Version/10.00'],
            ['Opera Mini', 'Opera/9.60 (J2ME/MIDP; Opera Mini/4.2.13918/488; U; en) Presto/2.2.0'],
            ['webOS', 'Mozilla/5.0 (webOS/1.0; U; en-US) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/1.0 Safari/525.27.1 Pre/1.0'],
            ['Playstation', 'Mozilla/5.0 (PLAYSTATION 3; 1.00)'],
            ['Windows CE', 'Mozilla/4.0 (compatible; MSIE 4.01; Windows CE; Sprint:PPC-6700; PPC; 240x320)'],
            ['Polaris', 'LG-LX600 Polaris/6.0 MMP/2.0 Profile/MIDP-2.1 Configuration/CLDC-1.1'],
            ['SEMC', 'SonyEricssonK608i/R2L/SN356841000828910 Browser/SEMC-Browser/4.2 Profile/MIDP-2.0 Configuration/CLDC-1.1'],
            ['NetFront', 'Mozilla/4.0 (compatible;MSIE 6.0;Windows95;PalmSource) Netfront/3.0;8;320x320'],
            ['Fennec', 'Mozilla/5.0 (X11; U; Linux armv61; en-US; rv:1.9.1b2pre) Gecko/20081015 Fennec/1.0a1'],
        ];
        
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
        $agents = ['mobile' => ['Foo']];
        $context = $this->newContext($agents);
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
        $agents = [
            ['Google', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'],
            ['Google', 'Mozilla/5.0 (compatible) Feedfetcher-Google; (+http://www.google.com/feedfetcher.html)'],
            ['Ask', 'Mozilla/5.0 (compatible; Ask Jeeves/Teoma; +http://about.ask.com/en/docs/about/webmasters.shtml)'],
            ['Baidu', 'Baiduspider+(+http://www.baidu.com/search/spider.htm)'],
            ['Yahoo', 'Mozilla/5.0 (compatible; Yahoo! Slurp/3.0; http://help.yahoo.com/help/us/ysearch/slurp)'],
            ['Nutch', 'GeoHasher/Nutch-1.0 (GeoHasher Web Search Engine; geohasher.gotdns.org;'],
            ['Y!J', 'Y!J-BRI/0.0.1 crawler ( http://help.yahoo.co.jp/help/jp/search/indexing/indexing-15.html)'],
            ['Danger hiptop', 'Mozilla/5.0 (Danger hiptop 3.3; U; AvantGo 3.2)'],
            ['MSR-ISRCCrawler', 'MSR-ISRCCrawler'],
            ['Y!OASIS', 'Y!OASIS/TEST no-ad Mozilla/4.08 [en] (X11; I; FreeBSD 2.2.8-STABLE i386)'],
            ['gsa-crawler', 'gsa-crawler (Enterprise; GID-01422; me@company.com)'],
            ['librabot' ,'librabot/1.0 (+http://search.msn.com/msnbot.htm)'],
            ['llssbot', 'llssbot/1.0(+http://labs.live.com;llssbot@microsoft.com)'],
            ['bingbot', 'Mozilla/5.0 (compatible; bingbot/2.0 +http://www.bing.com/bingbot.htm)'],
            ['MSMOBOT', 'msmobot/1.1 (+http://search.msn.com/msnbot.htm)'],
            ['MSNBot', 'msnbot-207-46-194-100.search.msn.com'],
            ['MSRBOT', 'MSRBOT (http://research.microsoft.com/research/sv/msrbot/)'],
            ['slurp', 'Slurp/2.0-condor_hourly (slurp@inktomi.com; http://www.inktomi.com/slurp.html)'],
            ['Scooter', 'Scooter/2.0 G.R.A.B. X2.0'],
            ['Yandex', 'Yandex/1.01.001 (compatible; Win16; I)'],
            ['Fast', 'FAST-WebCrawler/3.8 (atw-crawler at fast dot no; http://fast.no/support/crawler.asp)'],
            ['heritrix', 'Mozilla/5.0 (compatible; heritrix/1.12.1 +http://www.page-store.com) [email:paul@page-store.com]'],
            ['ia_archiver', 'ia_archiver/8.8 (Windows XP 7.2; en-US;)'],
            ['InternetArchive', 'internetarchive/0.8-dev (Nutch; http://lucene.apache.org/nutch/bot.html; nutch-agent@lucene.apache.org)'],
            ['archive.org_bot', 'Mozilla/5.0 (compatible; archive.org_bot/1.13.1x +http://crawler.archive.org)'],
            ['WordPress', 'wordpress/2.1.3'],
            ['Mp3Bot', 'Mozilla/5.0 (compatible; Mp3Bot/0.4; +http://mp3realm.org/mp3bot/)'],
            ['mp3Spider', 'mp3spider cn-search-devel'],
            ['Wget', 'Wget/1.12 (linux-gnu)'],
        ];
        
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
        $agents = ['crawler' => ['Foo']];
        $context = $this->newContext($agents);
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
        $this->assertSame(['foo' => 'bar'], $actual);
    }
    
    public function testGetPost()
    {
        $this->reset();
        $_POST['foo'] = 'bar';
        $context = $this->newContext();
        
        $actual = $context->getPost('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $context->getPost('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $context->getPost('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $context->getPost();
        $this->assertSame(['foo' => 'bar'], $actual);
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
        $this->assertSame(['foo' => 'bar'], $actual);
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
        $this->assertSame(['foo' => 'bar'], $actual);
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
        $this->assertSame(['foo' => 'bar'], $actual);
    }

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
        $agents = [
            'mobile' => ['foo'],
            'crawler' => ['bar'],
        ];
        
        $context = $this->newContext($agents);
    }
    
    public function testGetInput()
    {
        $this->reset();
        
        $object = (object) [
            'foo' => 'bar',
            'baz' => 'dib',
            'zim' => 'gir',
        ];
        
        $encode = json_encode($object);
        
        PhpStream::$content = $encode;
        
        $context = $this->newContext();
        
        $this->assertSame($encode, $context->getInput());
    }
    
    public function testGetJsonInput()
    {
        $this->reset();
        
        $object = (object) [
            'foo' => 'bar',
            'baz' => 'dib',
            'zim' => 'gir',
        ];
        
        $encode = json_encode($object);
        
        PhpStream::$content = $encode;
        
        $context = $this->newContext();
        
        $this->assertEquals($object, $context->getJsonInput());
    }
    
    public function testGetUrl()
    {
        $this->reset();
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/foo?bar=baz';
        $context = $this->newContext();
        
        $expect = 'http://example.com/foo?bar=baz';
        $actual = $context->getUrl();
        $this->assertSame($expect, $actual);
    }
}
